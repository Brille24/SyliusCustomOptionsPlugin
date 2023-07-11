<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValuePriceRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class CustomerOptionValueRefresher implements OrderProcessorInterface
{
    public function __construct(private CustomerOptionValuePriceRepositoryInterface $customerOptionValuePriceRepository)
    {
    }

    /**
     * {@inheritdoc}
     *
     * For more info have a look at this graphic `docs/images/OrderProcessor_Usage.png`
     */
    public function process(OrderInterface $order): void
    {
        $channel = $order->getChannel();
        foreach ($order->getItems() as $orderItem) {
            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            $this->copyOverValuesForOrderItem($orderItem, $channel);
        }
    }

    /** @inheritdoc */
    public function copyOverValuesForOrderItem(OrderItemInterface $orderItem, ChannelInterface $channel): void
    {
        $orderItemOptions = $orderItem->getCustomerOptionConfiguration();
        $product = $orderItem->getProduct();

        Assert::isInstanceOf($product, ProductInterface::class);

        foreach ($orderItemOptions as $orderItemOption) {
            // Gets the object reference to the customer option value

            /** @var CustomerOptionValueInterface|null $customerOptionValue */
            $customerOptionValue = $orderItemOption->getCustomerOptionValue();
            if ($customerOptionValue === null) {
                continue;
            }

            // This part of the process is needed to not only store the object reference but also update the copied
            // values on the entity so that if the reference changes the values stay the same
            $orderItemOption->setCustomerOptionValue($customerOptionValue);

            $price = $this->customerOptionValuePriceRepository->getPriceForChannel($channel, $product, $customerOptionValue);
            Assert::notNull($price, 'The customer option value "' . $customerOptionValue->getCode() . '" has no price');

            // Same here: Copy the price onto the customer option to be independent of the customer option value object.
            $orderItemOption->setPrice($price);
        }
    }
}
