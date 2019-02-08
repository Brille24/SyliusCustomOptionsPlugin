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
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatter;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class CustomerOptionValuePrice implements CustomerOptionValuePriceInterface
{
    /** @var int|null */
    private $id;

    /** @var float */
    private $percent = 0;

    /** @var int */
    private $amount = 0;

    /** @var string */
    private $type = CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT;

    /** @var CustomerOptionValueInterface|null */
    private $customerOptionValue;

    /** @var ProductInterface */
    private $product;

    /** @var ChannelInterface */
    private $channel;

    /** @var DateRange|null */
    private $dateValid;

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /** {@inheritdoc} */
    public function setPercent(float $percent): void
    {
        $this->percent = $percent;
    }

    /** {@inheritdoc} */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /** {@inheritdoc} */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /** {@inheritdoc} */
    public function getType(): string
    {
        return $this->type;
    }

    /** {@inheritdoc} */
    public function setType(string $type): void
    {
        $allTypes = self::getAllTypes();

        if (in_array($type, $allTypes, true)) {
            $this->type = $type;
        } else {
            throw new InvalidTypeException('Invalid type. Possible types are '.implode(', ', $allTypes));
        }
    }

    /** {@inheritdoc} */
    public static function getAllTypes(): array
    {
        return [
            self::TYPE_FIXED_AMOUNT,
            self::TYPE_PERCENT,
        ];
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
    public function __toString(): string
    {
        return $this->getValueString('USD', 'en_US', new MoneyFormatter());
    }

    /** {@inheritdoc} */
    public function getValueString(string $currencyCode, string $locale, MoneyFormatterInterface $formatter): string
    {
        if ($this->getType() === CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT) {
            return $formatter->format($this->getAmount(), $currencyCode, $locale);
        }

        $percent = $this->getPercent() * 100;

        return "{$percent}%";
    }

    /** {@inheritdoc} */
    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    /** {@inheritdoc} */
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    /** {@inheritdoc} */
    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    /** {@inheritdoc} */
    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    /** {@inheritdoc} */
    public function getDateValid(): ?DateRange
    {
        return $this->dateValid;
    }

    /** {@inheritdoc} */
    public function setDateValid(?DateRange $dateRange): void
    {
        $this->dateValid = $dateRange;
    }

    /** {@inheritdoc} */
    public function isActive(): bool
    {
        if ($this->dateValid === null) {
            return true;
        }

        return $this->dateValid->contains(new \DateTime('now'));
    }
}
