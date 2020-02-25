<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class CustomerOptionValueArchiver implements OrderItemValueCopierInterface
{
    /** @var ChannelInterface */
    private $currentChannel;

    public function __construct(ChannelInterface $currentChannel)
    {
        $this->currentChannel = $currentChannel;
    }

    /** {@inheritdoc} */
    public function copyOverValues(OrderInterface $order): void
    {
        foreach ($order->getItems() as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            $this->copyOverValuesForOrderItem($orderItem);
        }
    }

    /** {@inheritdoc} */
    public function copyOverValuesForOrderItem(OrderItemInterface $orderItem): void
    {
        $orderItemOptions = $orderItem->getCustomerOptionConfiguration();
        foreach ($orderItemOptions as $orderItemOption) {
            $customerOptionValue = $orderItemOption->getCustomerOptionValue();
            if ($customerOptionValue === null) {
                continue;
            }

            $orderItemOption->setCustomerOptionValue($customerOptionValue);

            $price = $customerOptionValue->getPriceForChannel($this->currentChannel);
            Assert::notNull($price);

            $orderItemOption->setPrice($price);
        }
    }
}
