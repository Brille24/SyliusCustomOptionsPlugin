<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;

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
    private $fixedPrice;

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
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
        return $this->customerOptionValueName;
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
    public function setCustomerOptionName(string $customerOptionName): void
    {
        $this->customerOptionName = $customerOptionName;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValueName(string $customerOptionValueName): void
    {
        $this->customerOptionValueName = $customerOptionValueName;
    }
}
