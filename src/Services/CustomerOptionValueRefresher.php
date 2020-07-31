<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class CustomerOptionValueRefresher implements OrderProcessorInterface
{
    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     *
     * For more info have a look at this graphic `docs/images/OrderProcessor_Usage.png`
     */
    public function process(OrderInterface $order): void
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
            // Gets the object reference to the customer option value
            $customerOptionValue = $orderItemOption->getCustomerOptionValue();
            if ($customerOptionValue === null) {
                continue;
            }

            // This part of the process is needed to not only store the object reference but also update the copied
            // values on the entity so that if the reference changes the values stay the same
            $orderItemOption->setCustomerOptionValue($customerOptionValue);

            $price = $customerOptionValue->getPriceForChannel($this->channelContext->getChannel());
            Assert::notNull($price);

            // Same here: Copy the price onto the customer option to be independent of the customer option value object.
            $orderItemOption->setPrice($price);
        }
    }
}
