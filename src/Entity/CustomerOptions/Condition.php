<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


class Condition implements ConditionInterface
{
    /** @var int */
    protected $id;

    /** @var CustomerOptionInterface */
    protected $customerOption;

    /** @var string */
    protected $comparator;

    /** @var int */
    protected $value;

    /** {@inheritdoc} */
    public function getCustomerOption(): CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /** {@inheritdoc} */
    public function setCustomerOption(CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;
    }

    /** {@inheritdoc} */
    public function getComparator(): string
    {
        return $this->comparator;
    }

    /** {@inheritdoc} */
    public function setComparator(string $comparator): void
    {
        $this->comparator = $comparator;
    }

    /** {@inheritdoc} */
    public function getValue(): int
    {
        return $this->value;
    }

    /** {@inheritdoc} */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->id;
    }
}