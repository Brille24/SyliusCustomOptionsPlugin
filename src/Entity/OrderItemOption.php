<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\{
    CustomerOptionInterface, CustomerOptionValueInterface, CustomerOptionValuePrice, CustomerOptionValuePriceInterface
};
use Sylius\Component\Core\Model\ChannelInterface;

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
        ChannelInterface $channel,
        CustomerOptionInterface $customerOption,
        $customerOptionValue
    ) {
        $this->setCustomerOption($customerOption);

        // Copying the customer option value
        if (is_scalar($customerOptionValue)) {
            $this->optionValue = $customerOptionValue;
        } elseif ($customerOptionValue instanceof CustomerOptionValueInterface) {
            $this->setCustomerOptionValue($customerOptionValue);

            $price = $customerOptionValue->getPriceForChannel($channel);
            $this->setPrice($price ?? new CustomerOptionValuePrice());
        }
    }

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getOrderItem(): OrderItemInterface
    {
        return $this->orderItem;
    }

    /** {@inheritdoc} */
    public function setOrderItem(OrderItemInterface $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    //<editor-fold desc="CustomerOptions">

    /** {@inheritdoc} */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;
        if ($customerOption !== null) {
            $this->customerOptionCode = $customerOption->getCode();
            $this->customerOptionName = $customerOption->getName();
        }
    }

    /** {@inheritdoc} */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionCode(): string
    {
        return $this->customerOptionCode;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionName(): string
    {
        return $this->customerOptionName;
    }
    //</editor-fold>

    //<editor-fold desc="CustomerOptionValue">
    public function setCustomerOptionValue(?CustomerOptionValueInterface $value): void
    {
        $this->customerOptionValue = $value;
        if ($value !== null) {
            $this->customerOptionValueCode = $value->getCode();
            $this->customerOptionValueName = $value->getName();
        }
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface
    {
        return $this->customerOptionValue;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValueCode(): ?string
    {
        return $this->customerOptionValueCode;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValueName(): string
    {
        return $this->customerOptionValueName ?? $this->optionValue;
    }

    /** {@inheritdoc} */
    public function getOptionValue(): ?string
    {
        return $this->optionValue;
    }
    //</editor-fold>

    /** {@inheritdoc} */
    public function setPrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->fixedPrice  = $price->getAmount();
        $this->percent     = $price->getPercent();
        $this->pricingType = $price->getType();
    }

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
    public function getPricingType(): string
    {
        return $this->pricingType;
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

    /** {@inheritdoc} */
    public function equals(OrderItemOptionInterface $orderItemOption): bool
    {
        $equals = $this->getCustomerOption() === $orderItemOption->getCustomerOption();
        $equals &= $this->getCustomerOptionValue() === $orderItemOption->getCustomerOptionValue();
        $equals &= $this->getOptionValue() === $orderItemOption->getOptionValue();


        return boolval($equals);
    }

    public function __toString()
    {
        return $this->customerOptionCode . ': ' . $this->getCustomerOptionValueName();
    }
}
