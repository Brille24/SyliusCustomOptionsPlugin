<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Services;


use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPricesRecalculator implements OrderProcessorInterface
{

    /**
     * @param OrderInterface $order
     */
    public function process(OrderInterface $order): void
    {
        foreach ($order->getItems() as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            $this->updateOrderItemConfiguration($orderItem->getCustomerOptionConfiguration());
        }
    }

    /**
     * @param OrderItemOptionInterface[] $orderItemConfiguration
     */
    public function updateOrderItemConfiguration(array $orderItemConfiguration): void
    {
        foreach ($orderItemConfiguration as $configuration) {
            $customerOptionValue = $configuration->getCustomerOptionValue();
            if ($customerOptionValue !== null) {
                $configuration->setCustomerOptionValue($customerOptionValue);
            }
        }
    }


}