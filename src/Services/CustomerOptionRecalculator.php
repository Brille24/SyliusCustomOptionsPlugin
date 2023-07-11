<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Event\RecalculateOrderItemOptionEvent;
use Brille24\SyliusCustomerOptionsPlugin\Event\RemoveCustomerOptionFromOrderEvent;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CustomerOptionRecalculator implements OrderProcessorInterface
{
    public const CUSTOMER_OPTION_ADJUSTMENT = 'customer_option';

    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function process(OrderInterface $order): void
    {
        $order->removeAdjustmentsRecursively(self::CUSTOMER_OPTION_ADJUSTMENT);
        $this->eventDispatcher->dispatch(new RemoveCustomerOptionFromOrderEvent($order));

        foreach ($order->getItems() as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            /** @var OrderItemOptionInterface[] $configuration */
            $configuration = $orderItem->getCustomerOptionConfiguration();

            foreach ($configuration as $orderItemOption) {
                $this->eventDispatcher->dispatch(new RecalculateOrderItemOptionEvent($orderItemOption));
            }
        }
    }
}
