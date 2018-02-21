<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Event;


use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class AddToCartListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var CustomerOptionRepositoryInterface
     */
    private $customerOptionRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        RequestStack $requestStack,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueResolverInterface $valueResolver,
        EntityManagerInterface $entityManager
    ) {
        $this->requestStack             = $requestStack;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->valueResolver            = $valueResolver;
        $this->entityManager            = $entityManager;
    }

    public function addItemToCart(ResourceControllerEvent $event): void
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        // If the order is null, it's an old order item with an existing reference in the database
        if ($orderItem->getOrder() === null) {
            return;
        }

        list($customerOptions, $customerOptionValues) = $this->getCustomerOptionsFromRequest($this->requestStack->getCurrentRequest());

        if (count($customerOptions) === 0) {
            return;
        }

        $salesOrderConfigurations = [];

        for ($i = 0; $i < count($customerOptions); $i++) {
            $salesOrderConfiguration = new OrderItemOption($customerOptions[$i], $customerOptionValues[$i]);
            $salesOrderConfiguration->setOrderItem($orderItem);
            $salesOrderConfigurations[] = $salesOrderConfiguration;
            $this->entityManager->persist($salesOrderConfiguration);
        }

        $orderItem->setCustomerOptionConfiguration($salesOrderConfigurations);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();
    }

    /**
     * Gets the customer options from the request
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCustomerOptionsFromRequest(Request $request): array
    {
        $addToCart = $request->request->get('sylius_add_to_cart');

        if (isset($addToCart['customerOptions'])) {
            $result               = [];
            $customerOptions      = [];
            $customerOptionValues = [];

            foreach ($addToCart['customerOptions'] as $code => $value) {
                $customerOption = $this->customerOptionRepository->findOneByCode($code);
                $optionData     = [];
                if ($customerOption !== null) {
                    $optionData['option'] = $customerOption;
                    $optionData['value']  = $this->valueResolver->resolve($customerOption, $value) ?? $value;

                    $result[] = $optionData;

                    $customerOptions[]      = $customerOption;
                    $customerOptionValues[] = $this->valueResolver->resolve($customerOption, $value) ?? $value;
                }
            }
            return [$customerOptions, $customerOptionValues];
        }
        return [[], []];
    }
}