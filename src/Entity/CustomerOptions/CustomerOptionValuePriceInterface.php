<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Entity\CustomerOptions;

interface CustomerOptionValuePriceInterface
{
    const TYPE_FIXED_AMOUNT = 'FIXED_AMOUNT';
    const TYPE_PERCENT = 'PERCENT';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
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
    public function getAllTypes(): array;

    /**
     * @return CustomerOptionValueInterface|null
     */
    public function getCustomerOptionValue(): ?CustomerOptionValueInterface;

    /**
     * @param CustomerOptionValueInterface|null $customerOptionValue
     */
    public function setCustomerOptionValue(?CustomerOptionValueInterface $customerOptionValue): void;
}