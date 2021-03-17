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
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Webmozart\Assert\Assert;

class OrderItemOption implements OrderItemOptionInterface
{
    /** @var int|null */
    private $id;

    /** @var OrderItemInterface */
    private $orderItem;

    /** @var CustomerOptionInterface|null */
    private $customerOption;

    /** @var string */
    private $customerOptionType;

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

    /** @var ?FileContent */
    private $fileContent;

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
            $this->customerOptionType = $customerOption->getType() ?? 'type';
        }
    }

    /** {@inheritdoc} */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionType(): string
    {
        return $this->customerOptionType;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionType(string $type): void
    {
        $this->customerOptionType = $type;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionCode(): string
    {
        return $this->customerOptionCode;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionCode(string $code): void
    {
        $this->customerOptionCode = $code;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionName(): string
    {
        if (null !== $this->customerOption) {
            return $this->customerOption->getName();
        }

        return $this->customerOptionName;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionName(string $name): void
    {
        $this->customerOptionName = $name;
    }

    //</editor-fold>

    //<editor-fold desc="CustomerOptionValue">

    /**
     * @param mixed $value
     */
    public function setCustomerOptionValue($value): void
    {
        if ($this->customerOptionType === CustomerOptionTypeEnum::FILE) {
            $this->optionValue         = 'file-content';
            $this->customerOptionValue = null;
            $this->fileContent         = new FileContent((string) $value);

            return;
        }

        $this->fileContent = null;

        if (is_scalar($value)) {
            $this->optionValue          = (string) $value;
            $this->customerOptionValue  = null;

            return;
        }

        if ($value !== null) {
            Assert::isInstanceOf($value, CustomerOptionValueInterface::class);

            $this->customerOptionValueCode = $value->getCode() ?? 'code';
            $this->customerOptionValueName = $value->getName() ?? 'name';
            $this->optionValue             = null;
        } else {
            $this->optionValue = '';
        }

        $this->customerOptionValue = $value;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface
    {
        return $this->customerOptionValue;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValueCode(?string $code): void
    {
        $this->customerOptionValueCode = $code;
    }

    /** {@inheritdoc} */
    public function getCustomerOptionValueCode(): ?string
    {
        return $this->customerOptionValueCode;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionValueName(?string $name): void
    {
        $this->customerOptionValueName = $name;
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
    public function setPricingType(string $type): void
    {
        $this->pricingType = $type;
    }

    /** {@inheritdoc} */
    public function getPricingType(): string
    {
        return $this->pricingType;
    }

    /** {@inheritdoc} */
    public function getScalarValue()
    {
        if ($this->fileContent instanceof FileContent) {
            return $this->fileContent->getContent();
        }

        return $this->optionValue ?? $this->customerOptionValueCode;
    }

    public function getFileContent(): ?FileContent
    {
        return $this->fileContent;
    }

    public function getCalculatedPrice(int $basePrice): int
    {
        if ($this->getPricingType() === CustomerOptionValuePrice::TYPE_PERCENT) {
            return (int) round($basePrice * $this->getPercent());
        }

        return $this->getFixedPrice();
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
