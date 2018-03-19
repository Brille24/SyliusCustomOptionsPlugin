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
        return $this->value['value'] ?? null;
    }

    /** {@inheritdoc} */
    public function setValue($value): void
    {
        $this->value = ConditionComparatorEnum::getValueConfig(
        $this->customerOption ? $this->customerOption->getType() : CustomerOptionTypeEnum::TEXT
        );

        if(CustomerOptionTypeEnum::isSelect($this->customerOption->getType())) {
            if(is_array($value)){
                $newValues = [];

                /** @var CustomerOptionValueInterface $val */
                foreach ($value as $val){
                    $newValues[] = $val->getCode();
                }

                $value = $newValues;
            }

            $this->value['value'] = is_array($value) ? $value : [];
        }else{
            $this->value['value'] = $value;
        }
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
    public function isMet($value): bool
    {
        switch ($this->comparator){
            case ConditionComparatorEnum::GREATER:
                return $value > $this->value;

            case ConditionComparatorEnum::GREATER_OR_EQUAL:
                return $value >= $this->value;

            case ConditionComparatorEnum::EQUAL:
                return $value == $this->value;

            case ConditionComparatorEnum::LESSER_OR_EQUAL:
                return $value <= $this->value;

            case ConditionComparatorEnum::LESSER:
                return $value < $this->value;


            case ConditionComparatorEnum::TRUE:
                return $value === true;

            case ConditionComparatorEnum::FALSE:
                return $value === false;


            case ConditionComparatorEnum::IN_SET:
                return in_array($value, $this->value);

            case ConditionComparatorEnum::NOT_IN_SET:
                return !in_array($value, $this->value);
        }

        return false;
    }
}