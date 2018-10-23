<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Webmozart\Assert\Assert;

trait ConditionTrait
{
    /** @var CustomerOptionInterface|null */
    protected $customerOption;

    /** @var string|null */
    protected $comparator;

    /** @var array|null */
    protected $value;

    /** @var ValidatorInterface|null */
    protected $validator;

    /** {@inheritdoc} */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /** {@inheritdoc} */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;

        $this->value = ConditionComparatorEnum::getValueConfig(
            $customerOption ? $customerOption->getType() : CustomerOptionTypeEnum::TEXT
        );
    }

    /** {@inheritdoc} */
    public function getComparator(): ?string
    {
        return $this->comparator;
    }

    /** {@inheritdoc} */
    public function setComparator(?string $comparator): void
    {
        Assert::true(in_array($comparator, ConditionComparatorEnum::getConstList(), true) || $comparator === null);

        $this->comparator = $comparator;
    }

    /** {@inheritdoc} */
    public function getValue(): ?array
    {
        $value = null;
        if ($this->value !== null
            && array_key_exists('value', $this->value)
            && $this->value['value'] !== null
        ) {
            $value = $this->value['value'];
        }

        if ($value !== null) {
            $value = ['value' => $value];
        }

        return $value;
    }

    /**
     * Sets a nex value
     *
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $value = is_array($value) && array_key_exists('value', $value) ? $value['value'] : $value;

        $newValue = ConditionComparatorEnum::getValueConfig(
            $this->customerOption ? $this->customerOption->getType() : CustomerOptionTypeEnum::TEXT
        );

        if ($newValue['type'] === 'array') {
            $newValue['value'] = is_array($value) ? $value : null;
        } elseif ($newValue['type'] === 'date') {
            $newValue['value'] = $value instanceof \DateTime ? $value : null;
        } elseif ($newValue['type'] === 'boolean') {
            $newValue['value'] = (bool) $value;
        } else {
            if (is_array($value) || $value instanceof \DateTime) {
                $newValue['value'] = null;
            } else {
                $newValue['value'] = $value;
            }
        }

        if ($newValue['value'] === null) {
            $newValue = ConditionComparatorEnum::getValueConfig(
                $this->customerOption ? $this->customerOption->getType() : CustomerOptionTypeEnum::TEXT
            );
        }

        $this->value = $newValue;
    }

    /** {@inheritdoc} */
    public function getValidator(): ?ValidatorInterface
    {
        return $this->validator;
    }

    /** {@inheritdoc} */
    public function setValidator(?ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed       $value
     * @param string|null $optionType
     *
     * @return bool
     */
    public function isMet($value, ?string $optionType = null): bool
    {
        if ($this->customerOption === null) {
            $optionType = 'number';
        } else {
            $optionType = $optionType ?? $this->customerOption->getType() ?? 'number';
        }

        $actual = $this->formatValue($value, $optionType);

        $target = $this->value['value'];

        if (CustomerOptionTypeEnum::isDate($optionType)) {
            $target = $target instanceof \DateTime ? $target : $this->formatValue($target, $optionType);
        } elseif (CustomerOptionTypeEnum::BOOLEAN === $optionType) {
            $target = $this->formatValue($target, $optionType);
        }

        if (!is_array($actual)) {
            $actual = [$actual];
        }

        $result = true;

        foreach ($actual as $val) {
            switch ($this->comparator) {
                case ConditionComparatorEnum::GREATER:
                    $result = $result ? $val > $target : false;

                    break;
                case ConditionComparatorEnum::GREATER_OR_EQUAL:
                    $result = $result ? $val >= $target : false;

                    break;
                case ConditionComparatorEnum::EQUAL:
                    $result = $result ? $val == $target : false;

                    break;
                case ConditionComparatorEnum::LESSER_OR_EQUAL:
                    $result = $result ? $val <= $target : false;

                    break;
                case ConditionComparatorEnum::LESSER:
                    $result = $result ? $val < $target : false;

                    break;
                case ConditionComparatorEnum::IN_SET:
                    $result = $result ? in_array($val, $target, true) : false;

                    break;
                case ConditionComparatorEnum::NOT_IN_SET:
                    $result = $result ? !in_array($val, $target, true) : false;

                    break;
            }
        }

        return $result;
    }

    /**
     * @param mixed  $value
     * @param string $optionType
     *
     * @return \DateTime|int|mixed
     */
    private function formatValue($value, string $optionType)
    {
        $result = $value;

        if ($optionType === CustomerOptionTypeEnum::TEXT) {
            $result = strlen($value);
        } elseif (CustomerOptionTypeEnum::isDate($optionType)) {
            if (isset($result['date']) && !is_array($result['date'])) {
                $result = new \DateTime($result['date']);
            } else {
                if ($optionType === CustomerOptionTypeEnum::DATETIME) {
                    $date   = $value['date'];
                    $time   = $value['time'];
                    $result = new \DateTime(sprintf('%d-%d-%d', $date['year'], $date['month'], $date['day']));
                    $result->setTime((int) ($time['hour']), (int) ($time['minute']));
                } else {
                    $result = new \DateTime(sprintf('%d-%d-%d', $value['year'], $value['month'], $value['day']));
                }
            }
        } elseif ($optionType === CustomerOptionTypeEnum::BOOLEAN) {
            $result = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $result;
    }
}
