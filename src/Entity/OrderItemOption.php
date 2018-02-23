<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

class OrderItemOption implements OrderItemOptionInterface
{
    /** @var int|null */
    private $id;

    /** @var OrderItemInterface */
    private $orderItem;

    /** @var CustomerOptionInterface|null */
    private $customerOption;

    /** @var string */
    private $customerOptionCode;

    /** @var string */
    private $customerOptionName;

    /** @var CustomerOptionValueInterface|null */
    private $customerOptionValue;

    /** @var string */
    private $customerOptionValueCode;

    /** @var string */
    private $customerOptionValueName;

    /** @var string */
    private $optionValue;

    /** @var int */
    private $fixedPrice = 0;

    /** @var string */
    private $pricingType;

    /** @var float */
    private $percent;

    public function __construct(
        CustomerOptionInterface $customerOption,
        $customerOptionValue,
        ChannelInterface $channel,
        \Sylius\Component\Core\Model\ProductInterface $product
    ) {
        // Copying the customer Option
        $this->customerOption     = $customerOption;
        $this->customerOptionCode = $customerOption->getCode();
        $this->customerOptionName = $customerOption->getName();

        // Copying the customer option value
        if (is_scalar($customerOptionValue)) {
            $this->optionValue = $customerOptionValue;
        } elseif ($customerOptionValue instanceof CustomerOptionValueInterface) {
            $this->customerOptionValue     = $customerOptionValue;
            $this->customerOptionValueCode = $customerOptionValue->getCode();
            $this->customerOptionValueName = $customerOptionValue->getName();

            // Getting the right price based on channel and product
            $price = null;

            if($product instanceof ProductInterface) {
                // Try to find a product specific price
                /** @var CustomerOptionValuePriceInterface $productPrice */
                foreach ($product->getCustomerOptionValuePrices() as $productPrice) {
                    if ($productPrice->getCustomerOptionValue() === $customerOptionValue && $productPrice->getChannel() === $channel) {
                        $price = $productPrice;
                        break;
                    }
                }
            }

            // If the product had no matching price configured, get the default price
            if($price === null) {
                /** @var CustomerOptionValuePriceInterface $price */
                foreach ($customerOptionValue->getPrices() as $defaultPrice) {
                    if ($defaultPrice->getChannel() === $channel) {
                        $price = $defaultPrice;
                        break;
                    }
                }
            }

            $this->pricingType             = $price->getType();
            $this->fixedPrice              = $price->getAmount() ?? 0;
            $this->percent                 = $price->getPercent() ?? 0;
        }
    }

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem(): OrderItemInterface
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItemInterface $orderItem
     */
    public function setOrderItem(OrderItemInterface $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    /** {@inheritdoc} */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /** {@inheritdoc} */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;
    }

    /** {@inheritdoc} */
    public function getOptionValue(): string
    {
        return $this->optionValue;
    }

    /** {@inheritdoc} */
    public function setOptionValue(string $optionValue): void
    {
        $this->optionValue = $optionValue;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionCode(): string
    {
        return $this->customerOptionCode;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionCode(string $customerOptionCode): void
    {
        $this->customerOptionCode = $customerOptionCode;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface
    {
        return $this->customerOptionValue;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValue(?CustomerOptionValueInterface $customerOptionValue): void
    {
        $this->customerOptionValue = $customerOptionValue;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValueCode(): string
    {
        return $this->customerOptionValueCode;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValueCode(string $customerOptionValueCode): void
    {
        $this->customerOptionValueCode = $customerOptionValueCode;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionName(): string
    {
        return $this->customerOptionName;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValueName(): string
    {
        return $this->customerOptionValueName ?? $this->optionValue;
    }

    /** {@inheritdoc} */
    public function setFixedPrice(int $price): void
    {
        $this->fixedPrice = $price;
    }

    /** {@inheritdoc} */
    public function getFixedPrice(): int
    {
        return $this->fixedPrice;
    }

    /** {@inheritdoc} */
    public function setPercent(float $percent): void
    {
        $this->percent = $percent;
    }

    /** {@inheritdoc} */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /** {@inheritdoc} */
    public function setPricingType(string $type): void
    {
        $this->pricingType = $type;
    }

    /** {@inheritdoc} */
    public function getPricingType(): string
    {
        return $this->pricingType;
    }


    /** {@inheritdoc} */
    public function setCustomerOptionName(string $customerOptionName): void
    {
        $this->customerOptionName = $customerOptionName;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValueName(string $customerOptionValueName): void
    {
        $this->customerOptionValueName = $customerOptionValueName;
    }

    /** {@inheritdoc} */
    public function getScalarValue()
    {
        if (is_null($this->optionValue)) {
            return $this->optionValue;
        } else {
            return $this->customerOptionValueCode;
        }
    }

}
