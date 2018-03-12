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

namespace Brille24\CustomerOptionsPlugin\Enumerations;

// TODO: Wait to see if sylius has something like this.
use DateTime;
use Exception;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CustomerOptionTypeEnum implements EnumInterface
{
    const __default = null;

    const TEXT = 'text';
    const SELECT = 'select';
    const MULTI_SELECT = 'multi_select';
    const FILE = 'file';
    const DATE = 'date';
    const DATETIME = 'datetime';
    const NUMBER = 'number';
    const BOOLEAN = 'boolean';

    public static function getConstList(): array
    {
        return [
            self::TEXT,
            self::SELECT,
            self::MULTI_SELECT,
            self::DATE,
            self::DATETIME,
            self::NUMBER,
            self::BOOLEAN,
        ];
    }

    public static function isValid($enumValue): bool
    {
        return in_array($enumValue, self::getConstList());
    }

    public static function getTranslateArray(): array
    {
        return [
            self::TEXT => 'brille24.form.customer_options.type.text',
            self::SELECT => 'brille24.form.customer_options.type.select',
            self::MULTI_SELECT => 'brille24.form.customer_options.type.multi_select',
            self::DATE => 'brille24.form.customer_options.type.date',
            self::DATETIME => 'brille24.form.customer_options.type.datetime',
            self::NUMBER => 'brille24.form.customer_options.type.number',
            self::BOOLEAN => 'brille24.form.customer_options.type.boolean',
        ];
    }

    public static function getFormTypeArray(): array
    {
        return [
            self::TEXT => [
                TextType::class,
                [],
            ],
            self::SELECT => [
                ChoiceType::class,
                [],
            ],
            self::MULTI_SELECT => [
                ChoiceType::class,
                ['multiple' => true],
            ],
            self::DATE => [
                DateType::class,
                [],
            ],
            self::DATETIME => [
                DateTimeType::class,
                [],
            ],
            self::NUMBER => [
                NumberType::class,
                [],
            ],
            self::BOOLEAN => [
                BooleanType::class,
                [],
            ],
        ];
    }

    /**
     * Gets the default configuration options of the types
     *
     * @param string $type
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getConfigurationArray(): array
    {
        return [
            self::TEXT => [
                'brille24.form.config.min.length' => ['type' => 'integer', 'value' => 0],
                'brille24.form.config.max.length' => ['type' => 'integer', 'value' => 255],
            ],
            self::DATE => [
                'brille24.form.config.min.date' => ['type' => 'date', 'value' => new DateTime('1900-01-01')],
                'brille24.form.config.max.date' => ['type' => 'date', 'value' => new DateTime('3000-12-31')],
            ],
            self::DATETIME => [
                'brille24.form.config.min.date' => ['type' => 'datetime', 'value' => new DateTime('1900-01-01')],
                'brille24.form.config.max.date' => ['type' => 'datetime', 'value' => new DateTime('3000-12-31')],
            ],
            self::NUMBER => [
                'brille24.form.config.min.number' => ['type' => 'integer', 'value' => 0],
                'brille24.form.config.max.number' => ['type' => 'integer', 'value' => 1000],
            ],
            self::BOOLEAN => [
                'brille24.form.config.default_value' => ['type' => 'boolean', 'value' => true],
            ],
        ];
    }

    public static function isSelect(string $type): bool
    {
        return in_array($type, [self::SELECT, self::MULTI_SELECT]);
    }

    public static function isDate(string $type): bool
    {
        return in_array($type, [self::DATE, self::DATETIME]);
    }
}
