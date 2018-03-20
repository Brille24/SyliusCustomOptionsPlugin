<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Traits;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\ValidatorInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;

trait ConditionTrait
{
    /** @var CustomerOptionInterface */
    protected $customerOption;

    /** @var string */
    protected $comparator;

    /** @var array */
    protected $value;

    /** @var ValidatorInterface */
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
        $this->comparator = $comparator;
    }

    /** {@inheritdoc} */
    public function getValue()
    {
        $value = $this->value['value'] ?? null;

        if($value !== null){
            $value = ['value' => $value];
        }

        return $value;
    }

    /** {@inheritdoc} */
    public function setValue($value): void
    {
        $value = key_exists('value', $value) ? $value['value'] : $value;

        $newValue = ConditionComparatorEnum::getValueConfig(
        $this->customerOption ? $this->customerOption->getType() : CustomerOptionTypeEnum::TEXT
        );

        if($newValue['type'] === 'array')
        {
            $newValue['value'] = is_array($value) ? $value : null;
        }
        elseif ($newValue['type'] === 'date')
        {
            $newValue['value'] = $value instanceof \DateTime ? $value : null;
        }
        elseif ($newValue['type'] === 'boolean')
        {
            $newValue['value'] = boolval($value);
        } else {
            if(is_array($value) || $value instanceof \DateTime)
            {
                $newValue['value'] = null;
            }else{
                $newValue['value'] = $value;
            }
        }

        if($newValue['value'] === null){
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

    /** {@inheritdoc} */
    public function isMet($value, string $optionType = 'number'): bool
    {
        if($optionType === CustomerOptionTypeEnum::TEXT){
            $actual = strlen($value);
        }else{
            $actual = $value;
        }

        $target = $this->value['value'];

        switch ($this->comparator){
            case ConditionComparatorEnum::GREATER:
                return $actual > $target;

            case ConditionComparatorEnum::GREATER_OR_EQUAL:
                return $actual >= $target;

            case ConditionComparatorEnum::EQUAL:
                return $actual == $target;

            case ConditionComparatorEnum::LESSER_OR_EQUAL:
                return $actual <= $target;

            case ConditionComparatorEnum::LESSER:
                return $actual < $target;


            case ConditionComparatorEnum::IN_SET:
                return in_array($actual, $target);

            case ConditionComparatorEnum::NOT_IN_SET:
                return !in_array($actual, $target);
        }

        return false;
    }
}