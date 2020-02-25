<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
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

            $this->addOrderItemAdjustment($orderItem);
        }
    }

    private function addOrderItemAdjustment(OrderItemInterface $orderItem): void
    {
        /** @var OrderItemOptionInterface[] $configuration */
        $configuration = $orderItem->getCustomerOptionConfiguration();
        foreach ($configuration as $orderItemOption) {
            // Skip all customer options that don't have customer option values as they can not have a price like
            // text options
            if ($orderItemOption->getCustomerOptionValue() === null) {
                continue;
            }

            $adjustment = $this->adjustmentFactory->createWithData(
                self::CUSTOMER_OPTION_ADJUSTMENT,
                $orderItemOption->getCustomerOptionName(),
                $orderItemOption->getCalculatedPrice($orderItem->getUnitPrice())
            );

            foreach ($orderItem->getUnits() as $unit) {
                $unit->addAdjustment($adjustment);
            }
        }
    }
}
