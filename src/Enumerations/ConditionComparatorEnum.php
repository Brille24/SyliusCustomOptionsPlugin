<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Enumerations;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class ConditionComparatorEnum implements EnumInterface
{
    const GREATER          = 'greater';
    const GREATER_OR_EQUAL = 'greater_equal';
    const EQUAL            = 'equal';
    const LESSER_OR_EQUAL  = 'lesser_equal';
    const LESSER           = 'lesser';

    const IN_SET     = 'in_set';
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

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValid($value): bool
    {
        return in_array($value, self::getConstList(), true);
    }

    public static function getTranslateArray(): array
    {
        return [
            self::GREATER          => 'brille24.form.validators.condition.greater',
            self::GREATER_OR_EQUAL => 'brille24.form.validators.condition.greater_equal',
            self::EQUAL            => 'brille24.form.validators.condition.equal',
            self::LESSER_OR_EQUAL  => 'brille24.form.validators.condition.lesser_equal',
            self::LESSER           => 'brille24.form.validators.condition.lesser',

            self::IN_SET     => 'brille24.form.validators.condition.in_set',
            self::NOT_IN_SET => 'brille24.form.validators.condition.not_in_set',
        ];
    }

    public static function transformToTranslateArray(array $values): array
    {
        $result = [];

        foreach ($values as $value) {
            if (self::isValid($value)) {
                $result[$value] = self::getTranslateArray()[$value];
            }
        }

        return $result;
    }

    public static function getValuesForCustomerOptionType(string $customerOptionType): array
    {
        if (CustomerOptionTypeEnum::isSelect($customerOptionType)) {
            return [
                self::IN_SET,
                self::NOT_IN_SET,
            ];
        }
        if ($customerOptionType === CustomerOptionTypeEnum::BOOLEAN) {
            return [
                self::EQUAL,
            ];
        }

        return [
                self::GREATER,
                self::GREATER_OR_EQUAL,
                self::EQUAL,
                self::LESSER_OR_EQUAL,
                self::LESSER,
            ];
    }

    public static function getFormTypeForCustomerOptionType(string $type): array
    {
        if (CustomerOptionTypeEnum::isSelect($type)) {
            return [
                ChoiceType::class,
                [
                    'multiple' => true,
                    'label'    => 'brille24.form.validators.fields.value.set',
                ],
            ];
        }
        if ($type === CustomerOptionTypeEnum::TEXT) {
            return [
                IntegerType::class,
                [
                    'label' => 'brille24.form.validators.fields.value.text',
                ],
            ];
        }

        return CustomerOptionTypeEnum::getFormTypeArray()[$type];
    }

    public static function getValueConfig(string $type): array
    {
        if (CustomerOptionTypeEnum::isSelect($type)) {
            return [
                'type'  => 'array',
                'value' => [],
            ];
        }
        if (CustomerOptionTypeEnum::isDate($type)) {
            return [
                'type'  => 'date',
                'value' => new \DateTime(),
            ];
        }
        if ($type === CustomerOptionTypeEnum::BOOLEAN) {
            return [
                'type'  => 'boolean',
                'value' => true,
            ];
        }

        return [
                'type'  => 'integer',
                'value' => 0,
            ];
    }
}
