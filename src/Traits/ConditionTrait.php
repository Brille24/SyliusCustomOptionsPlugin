<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Traits;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Webmozart\Assert\Assert;

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
        Assert::true(in_array($comparator, ConditionComparatorEnum::getConstList()) || $comparator === null);

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
        $value = is_array($value) && key_exists('value', $value) ? $value['value'] : $value;

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
    public function isMet($value, ?string $optionType = null): bool
    {
        $optionType = $optionType ?? $this->customerOption->getType() ?? 'number';

        if($optionType === CustomerOptionTypeEnum::TEXT){
            $actual = strlen($value);
        }elseif (CustomerOptionTypeEnum::isDate($optionType)){
            $actual = new \DateTime();
            $actual->setDate(
                intval($value['year']),
                intval($value['month']),
                intval($value['day'])
            );

            if($optionType === CustomerOptionTypeEnum::DATETIME){
                $actual->setTime($value['hour'], $value['minute'], $value['second']);
            }
        }else{
            $actual = $value;
        }

        if($this->value['type'] === 'date'){
            $target = new \DateTime($this->value['value']['date']);
            $target->setTimezone(new \DateTimeZone($this->value['value']['timezone']));
        }else{
            $target = $this->value['value'];
        }

        if(!is_array($actual)){
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
                    $result = $result ? in_array($val, $target) : false;
                    break;

                case ConditionComparatorEnum::NOT_IN_SET:
                    $result = $result ? !in_array($val, $target) : false;
                    break;
            }
        }

        return $result;
    }
}