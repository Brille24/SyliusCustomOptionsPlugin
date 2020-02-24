<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPricesRecalculator implements OrderProcessorInterface
{
    /** @var ChannelInterface */
    private $currentChannel;

    public function __construct(ChannelInterface $currentChannel)
    {
        $this->currentChannel = $currentChannel;
    }

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
    private function updateOrderItemConfiguration(array $orderItemConfiguration): void
    {
        foreach ($orderItemConfiguration as $configuration) {
            $customerOptionValue = $configuration->getCustomerOptionValue();
            if ($customerOptionValue === null) {
                continue;
            }

            $configuration->setCustomerOptionValue($customerOptionValue);
            $price = $customerOptionValue->getPriceForChannel($this->currentChannel);
            $configuration->setPrice($price);
        }
    }
}
