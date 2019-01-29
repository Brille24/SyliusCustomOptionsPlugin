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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionValuePriceInterface extends ResourceInterface
{
    const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';
    const TYPE_PERCENT      = 'PERCENT';

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
    public function getType(): string;

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
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface;

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product): void;

    /**
     * @param ChannelInterface $channel
     */
    public function setChannel(?ChannelInterface $channel): void;

    /**
     * @return ChannelInterface
     */
    public function getChannel(): ?ChannelInterface;

    /**
     * Returns a string representing the value of the object
     *
     * @param string                  $currencyCode
     * @param string                  $locale
     * @param MoneyFormatterInterface $formatter
     *
     * @return string
     */
    public function getValueString(string $currencyCode, string $locale, MoneyFormatterInterface $formatter): string;

    /**
     * @return DateRange|null
     */
    public function getDateValid(): ?DateRange;

    /**
     * @param DateRange|null $dateRange
     */
    public function setDateValid(?DateRange $dateRange): void;

    /**
     * @return bool
     */
    public function isActive(): bool;
}
