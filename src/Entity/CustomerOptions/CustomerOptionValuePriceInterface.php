<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface CustomerOptionValuePriceInterface
{
    const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';
    const TYPE_PERCENT = 'PERCENT';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Returns the amount of percent it reduces. So 10.3% will be 10.3 in the field. Getting the decimal representation
     * you need to divide by 100 first.
     *
     * @return float
     */
    public function getPercent(): float;

    /**
     * @param float $percent
     */
    public function setPercent(float $percent): void;

    /**
     * @return int
     */
    public function getAmount(): int;

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void;

    /**
     * @return string
     */
    public function getType(): ?string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * Returns all possible type values for the setType function
     *
     * @return array
     */
    public static function getAllTypes(): array;

    /**
     * @return CustomerOptionValueInterface|null
     */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface;

    /**
     * @param CustomerOptionValueInterface|null $customerOptionValue
     */
    public function setCustomerOptionValue(?CustomerOptionValueInterface $customerOptionValue): void;

    /**
     * @return string|null
     */
    public function getCustomerOptionValueName(): ?string;

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface;

    /**
     * @param ProductInterface|null $product
     */
    public function setProduct(?ProductInterface $product): void;

    /**
     * @param ChannelInterface $channel
     */
    public function setChannel(?ChannelInterface $channel): void;

    /**
     * @return ChannelInterface
     */
    public function getChannel(): ?ChannelInterface;
}
