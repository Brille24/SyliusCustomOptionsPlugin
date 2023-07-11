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
    public function getId(): ?int;

    public function getOrderItem(): OrderItemInterface;

    public function setOrderItem(OrderItemInterface $orderItem): void;

    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    public function getCustomerOption(): ?CustomerOptionInterface;

    public function getCustomerOptionType(): string;

    public function setCustomerOptionType(string $type): void;

    public function getCustomerOptionCode(): string;

    public function setCustomerOptionCode(string $code): void;

    public function getCustomerOptionName(): string;

    public function setCustomerOptionName(string $name): void;

    public function setOptionValue(?string $value): void;

    /**
     * @return string
     */
    public function getOptionValue(): ?string;

    /**
     * @param mixed $customerOptionValue
     */
    public function setCustomerOptionValue($customerOptionValue): void;

    public function getCustomerOptionValue(): ?CustomerOptionValueInterface;

    /**
     * @return string
     */
    public function getCustomerOptionValueCode(): ?string;

    public function setCustomerOptionValueCode(?string $code): void;

    public function getCustomerOptionValueName(): string;

    public function setCustomerOptionValueName(?string $name): void;

    public function setPrice(CustomerOptionValuePriceInterface $price): void;

    public function setFixedPrice(int $price): void;

    public function getFixedPrice(): int;

    public function setPercent(float $percent): void;

    public function getPercent(): float;

    public function setPricingType(string $type): void;

    public function getPricingType(): string;

    /**
     * @return mixed
     */
    public function getScalarValue();

    public function getCalculatedPrice(int $basePrice): int;

    public function equals(self $orderItemOption): bool;

    public function getFileContent(): ?FileContent;
}
