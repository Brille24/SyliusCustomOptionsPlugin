<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\EventListener;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Factory\OrderItemOptionFactory;
use Brille24\CustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /** @var OrderItemOptionFactoryInterface */
    private $orderItemOptionFactory;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        OrderItemOptionFactoryInterface $itemOptionFactory
    ) {
        $this->requestStack             = $requestStack;
        $this->entityManager            = $entityManager;
        $this->orderItemOptionFactory   = $itemOptionFactory;
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

        $salesOrderConfigurations = [];
        foreach ($customerOptionConfiguration as $customerOptionCode => $valueArray) {
            if(!is_array($valueArray)){
                $valueArray = [$valueArray];
            }

            foreach ($valueArray as $value) {
                // Creating the item
                $salesOrderConfiguration = $this->orderItemOptionFactory->createNewFromStrings(
                    $customerOptionCode,
                    $value
                );

                $salesOrderConfiguration->setOrderItem($orderItem);

                $this->entityManager->persist($salesOrderConfiguration);

                $salesOrderConfigurations[] = $salesOrderConfiguration;
            }
        }

        $orderItem->setCustomerOptionConfiguration($salesOrderConfigurations);
        $orderItem->recalculateUnitsTotal();

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

        return $addToCart['customerOptions'];
    }
}