<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Enumerations;


use Brille24\CustomerOptionsPlugin\Form\CustomDateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class ConditionComparatorEnum implements EnumInterface
{
    const GREATER = 'greater';
    const GREATER_OR_EQUAL = 'greater_equal';
    const EQUAL = 'equal';
    const LESSER_OR_EQUAL = 'lesser_equal';
    const LESSER = 'lesser';

    const IN_SET = 'in_set';
    const NOT_IN_SET = 'not_in_set';

    public static function getConstList(): array
    {
        return [
            self::GREATER,
            self::GREATER_OR_EQUAL,
            self::EQUAL,
            self::LESSER_OR_EQUAL,
            self::LESSER,

            self::IN_SET,
            self::NOT_IN_SET,
        ];
    }

    public static function isValid($value): bool
    {
        return in_array($value, self::getConstList());
    }

    public static function getTranslateArray(): array
    {
        return [
            self::GREATER => 'brille24.form.validators.condition.greater',
            self::GREATER_OR_EQUAL => 'brille24.form.validators.condition.greater_equal',
            self::EQUAL => 'brille24.form.validators.condition.equal',
            self::LESSER_OR_EQUAL => 'brille24.form.validators.condition.lesser_equal',
            self::LESSER => 'brille24.form.validators.condition.lesser',

            self::IN_SET => 'brille24.form.validators.condition.in_set',
            self::NOT_IN_SET => 'brille24.form.validators.condition.not_in_set',
        ];
    }

    public static function transformToTranslateArray(array $values): array
    {
        $result = [];

        foreach ($values as $value){
            if(self::isValid($value)){
                $result[$value] = self::getTranslateArray()[$value];
            }
        }

        return $result;
    }

    public static function getValuesForCustomerOptionType(string $customerOptionType): array
    {
        if(CustomerOptionTypeEnum::isSelect($customerOptionType)){
            return [
                self::IN_SET,
                self::NOT_IN_SET,
            ];
        }elseif ($customerOptionType === CustomerOptionTypeEnum::BOOLEAN){
            return [
                self::EQUAL,
            ];
        }else{
            return [
                self::GREATER,
                self::GREATER_OR_EQUAL,
                self::EQUAL,
                self::LESSER_OR_EQUAL,
                self::LESSER,
            ];
        }
    }

    public static function getFormTypeForCustomerOptionType(string $type): array
    {
        if(CustomerOptionTypeEnum::isSelect($type)){
            return [
                ChoiceType::class,
                ['multiple' => true],
            ];
        }elseif ($type === CustomerOptionTypeEnum::BOOLEAN){
            return [
                CheckboxType::class,
                [],
            ];
        }elseif (CustomerOptionTypeEnum::isDate($type)){
            return [
                CustomDateType::class,
                [],
            ];
        }else{
            return [
                NumberType::class,
                [],
            ];
        }
    }

    public static function getValueConfig(string $type): array
    {
        if(CustomerOptionTypeEnum::isSelect($type)){
            return [];
        }elseif (CustomerOptionTypeEnum::isDate($type)){
            return [
                'type' => 'date',
                'value' => new \DateTime(),
            ];
        }elseif ($type === CustomerOptionTypeEnum::BOOLEAN){
            return [
                'type' => 'boolean',
                'value' => true,
            ];
        }else{
            return [
                'type' => 'integer',
                'value' => 0,
            ];
        }
    }
}