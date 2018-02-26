<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Event;


use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\Channel;
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

        $customerOptionConfiguration = $this->getCustomerOptionsFromRequest($this->requestStack->getCurrentRequest());

        if (count($customerOptionConfiguration) === 0) {
            return;
        }

        $salesOrderConfigurations = [];

        $channel = new Channel();
        foreach ($customerOptionConfiguration as $configuration) {
            $salesOrderConfiguration = new OrderItemOption($channel, $configuration['option'], $configuration['value']);
            $salesOrderConfiguration->setOrderItem($orderItem);

            $this->entityManager->persist($salesOrderConfiguration);

            $salesOrderConfigurations[] = $salesOrderConfiguration;
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

        if (!isset($addToCart['customerOptions'])) {
            return [];
        }

        $result = [];
        foreach ($addToCart['customerOptions'] as $code => $value) {
            $customerOption = $this->customerOptionRepository->findOneByCode($code);

            if ($customerOption !== null) {
                $result[] = [
                    'option' => $customerOption,
                    'value'  => $this->valueResolver->resolve($customerOption, $value) ?? $value,
                ];
            }
        }
        return $result;
    }
}