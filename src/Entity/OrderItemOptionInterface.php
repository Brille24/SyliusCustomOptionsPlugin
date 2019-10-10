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
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderItemOptionInterface extends ResourceInterface
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
     * @param CustomerOptionInterface|null $customerOption
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    /**
     * @return CustomerOptionInterface|null
     */
    public function getCustomerOption(): ?CustomerOptionInterface;

    /**
     * @return string
     */
    public function getCustomerOptionType(): string;

    /**
     * @param string $type
     */
    public function setCustomerOptionType(string $type): void;

    /**
     * @return string
     */
    public function getCustomerOptionCode(): string;

    /**
     * @param string $code
     */
    public function setCustomerOptionCode(string $code): void;

    /**
     * @return string
     */
    public function getCustomerOptionName(): string;

    /**
     * @param string $name
     */
    public function setCustomerOptionName(string $name): void;

    /**
     * @param string|null $value
     */
    public function setOptionValue(?string $value): void;

    /**
     * @return string
     */
    public function getOptionValue(): ?string;

    /**
     * @param mixed $customerOptionValue
     */
    public function setCustomerOptionValue($customerOptionValue): void;

    /**
     * @return CustomerOptionValueInterface|null
     */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface;

    /**
     * @return string
     */
    public function getCustomerOptionValueCode(): ?string;

    /**
     * @param string|null $code
     */
    public function setCustomerOptionValueCode(?string $code): void;

    /**
     * @return string
     */
    public function getCustomerOptionValueName(): string;

    /**
     * @param string|null $name
     */
    public function setCustomerOptionValueName(?string $name): void;

    /**
     * @param CustomerOptionValuePriceInterface $price
     */
    public function setPrice(CustomerOptionValuePriceInterface $price): void;

    /**
     * @param int $price
     */
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
     * @param string $type
     */
    public function setPricingType(string $type): void;

    /**
     * @return string
     */
    public function getPricingType(): string;

    /**
     * @return mixed
     */
    public function getScalarValue();

    /**
     * @param OrderItemOptionInterface $orderItemOption
     *
     * @return bool
     */
    public function equals(self $orderItemOption): bool;
}
