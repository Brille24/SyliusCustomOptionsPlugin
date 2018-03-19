<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


class Condition implements ConditionInterface
{
    const GREATER = 'greater';
    const GREATER_OR_EQUAL = 'greater_equal';
    const EQUAL = 'equal';
    const LESSER_OR_EQUAL = 'lesser_equal';
    const LESSER = 'lesser';

    /** @var int */
    protected $id;

    /** @var CustomerOptionInterface */
    protected $customerOption;

    /** @var string */
    protected $comparator;

    /** @var int */
    protected $value;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct()
    {
        $this->value = 0;
    }

    /** {@inheritdoc} */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /** {@inheritdoc} */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;
    }

    /** {@inheritdoc} */
    public function getComparator(): ?string
    {
        return $this->comparator;
    }

    /** {@inheritdoc} */
    public function setComparator(?string $comparator): void
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
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /** {@inheritdoc} */
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function isMet($value): bool
    {
        switch ($this->comparator){
            case self::GREATER:
                return $value > $this->value;

            case self::GREATER_OR_EQUAL:
                return $value >= $this->value;

            case self::EQUAL:
                return $value == $this->value;

            case self::LESSER_OR_EQUAL:
                return $value <= $this->value;

            case self::LESSER:
                return $value < $this->value;
        }

        return false;
    }
}