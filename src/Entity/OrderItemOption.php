<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
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

    /** @var string|null */
    private $optionValue;

    /** @var int */
    private $fixedPrice = 0;

    /** @var string */
    private $pricingType = '';

    /** @var float */
    private $percent = 0;

    /**
     * OrderItemOption constructor.
     *
     * @param ChannelInterface        $channel
     * @param CustomerOptionInterface $customerOption
     * @param mixed                   $customerOptionValue
     */
    public function __construct(
        ChannelInterface $channel,
        CustomerOptionInterface $customerOption,
        $customerOptionValue
    ) {
        $this->setCustomerOption($customerOption);

        // Copying the customer option value
        if (is_scalar($customerOptionValue)) {
            $this->optionValue = (string) $customerOptionValue;
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
            $this->customerOptionCode = $customerOption->getCode() ?? 'code';
            $this->customerOptionName = $customerOption->getName() ?? 'name';
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
        if (null !== $this->customerOption) {
            return $this->customerOption->getName();
        }

        return $this->customerOptionName;
    }

    //</editor-fold>

    //<editor-fold desc="CustomerOptionValue">
    public function setCustomerOptionValue(?CustomerOptionValueInterface $value): void
    {
        $this->customerOptionValue = $value;
        if ($value !== null) {
            $this->customerOptionValueCode = $value->getCode() ?? 'code';
            $this->customerOptionValueName = $value->getName() ?? 'name';
            $this->optionValue             = null;
        } else {
            $this->optionValue = $value ?? '';
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
        if (null !== $this->customerOptionValue) {
            return $this->customerOptionValue->getName();
        }

        return $this->customerOptionValueName ?? ($this->optionValue ?? '');
    }

    /** {@inheritdoc} */
    public function setOptionValue(?string $value): void
    {
        $this->optionValue = $value;
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
        if (null !== $this->optionValue) {
            return $this->optionValue;
        }

        return $this->customerOptionValueCode;
    }

    /** {@inheritdoc} */
    public function equals(OrderItemOptionInterface $orderItemOption): bool
    {
        $equals = $this->getCustomerOption() === $orderItemOption->getCustomerOption();
        $equals &= $this->getCustomerOptionValue() === $orderItemOption->getCustomerOptionValue();
        $equals &= $this->getOptionValue() === $orderItemOption->getOptionValue();

        return (bool) $equals;
    }

    public function __toString()
    {
        return $this->customerOptionCode.': '.$this->getCustomerOptionValueName();
    }
}
