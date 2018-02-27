<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;

interface OrderItemOptionInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem(): OrderItemInterface;

    /**
     * @param OrderItemInterface $orderItem
     */
    public function setOrderItem(OrderItemInterface $orderItem): void;

    /**
     * @return CustomerOptionInterface|null
     */
    public function getCustomerOption(): ?CustomerOptionInterface;

    /**
     * @param CustomerOptionInterface|null $customerOption
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    /**
     * @return string
     */
    public function getOptionValue(): ?string;

    /**
     * @param string $optionValue
     */
    public function setOptionValue(?string $optionValue): void;

    /**
     * @return string
     */
    public function getCustomerOptionCode(): ?string;

    /**
     * @param string $customerOptionCode
     */
    public function setCustomerOptionCode(string $customerOptionCode): void;

    /**
     * @return CustomerOptionValueInterface|null
     */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface;

    /**
     * @param CustomerOptionValueInterface|null $customerOptionValue
     */
    public function setCustomerOptionValue(?CustomerOptionValueInterface $customerOptionValue): void;

    /**
     * @return string
     */
    public function getCustomerOptionValueCode(): ?string;

    /**
     * @param string $customerOptionValueCode
     */
    public function setCustomerOptionValueCode(?string $customerOptionValueCode): void;

    /**
     * @return string
     */
    public function getCustomerOptionName(): string;

    /**
     * @param string $customerOptionName
     */
    public function setCustomerOptionName(string $customerOptionName): void;

    /**
     * @return string
     */
    public function getCustomerOptionValueName(): string;

    /**
     * @param string $customerOptionValueName
     */
    public function setCustomerOptionValueName(string $customerOptionValueName): void;

    /**
     * @param CustomerOptionValuePriceInterface $price
     */
    public function setPrice(CustomerOptionValuePriceInterface $price): void;

    public function setFixedPrice(int $price): void;

    /**
     * @return int
     */
    public function getFixedPrice(): int;

    /**
     * @param float $percent
     */
    public function setPercent(float $percent): void;

    /**
     * @return float
     */
    public function getPercent(): float;

    /**
     * @return string
     */
    public function getPricingType(): string;

    /**
     * @return mixed
     */
    public function getScalarValue();

    /** {@inheritdoc} */
    public function equals(self $orderItemOption): bool;

}
