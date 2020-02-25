<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class CustomerOptionRecalculator implements OrderProcessorInterface
{
    public const CUSTOMER_OPTION_ADJUSTMENT = 'CUSTOMER_OPTION_ADJUSTMENT';

    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;

    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function process(OrderInterface $order): void
    {
        foreach ($order->getItems() as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            $this->addOrderItemAdjustment($orderItem, $orderItem->getCustomerOptionConfiguration());
        }
    }

    private function addOrderItemAdjustment(OrderItemInterface $orderItem, array $configuration): void
    {
        foreach ($configuration as $value) {
            /** @var OrderItemOptionInterface $value */
            if ($value->getCustomerOptionValue() === null) {
                continue; // Skip all values where the value is not an object (value objects can be priced)
            }

            $adjustment = $this->adjustmentFactory->createWithData(
                self::CUSTOMER_OPTION_ADJUSTMENT,
                $value->getCustomerOptionName(),
                $value->getCalculatedPrice($orderItem->getUnitPrice())
            );
            $this->assignAdjustmentToOrderItemUnit($adjustment, $orderItem);
        }
    }

    private function assignAdjustmentToOrderItemUnit(
        AdjustmentInterface $adjustment,
        OrderItemInterface $orderItem
    ): void {
        foreach ($orderItem->getUnits() as $unit) {
            $unit->addAdjustment($adjustment);
        }
    }
}
