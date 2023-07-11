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
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRangeInterface;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionValuePriceInterface extends ResourceInterface
{
    public const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';

    public const TYPE_PERCENT = 'PERCENT';

    public function getId(): ?int;

    /**
     * Returns the amount of percent it reduces. So 10.3% will be 10.3 in the field. Getting the decimal representation
     * you need to divide by 100 first.
     */
    public function getPercent(): float;

    public function setPercent(float $percent): void;

    public function getAmount(): int;

    public function setAmount(int $amount): void;

    public function getType(): string;

    public function setType(string $type): void;

    /**
     * Returns all possible type values for the setType function
     */
    public static function getAllTypes(): array;

    public function getCustomerOptionValue(): ?CustomerOptionValueInterface;

    public function setCustomerOptionValue(?CustomerOptionValueInterface $customerOptionValue): void;

    public function getProduct(): ?ProductInterface;

    public function setProduct(?ProductInterface $product): void;

    public function setChannel(?ChannelInterface $channel): void;

    public function getChannel(): ?ChannelInterface;

    /**
     * Returns a string representing the value of the object
     */
    public function getValueString(string $currencyCode, string $locale, MoneyFormatterInterface $formatter): string;

    public function getDateValid(): ?DateRangeInterface;

    public function setDateValid(?DateRangeInterface $dateRange): void;

    public function isActive(): bool;
}
