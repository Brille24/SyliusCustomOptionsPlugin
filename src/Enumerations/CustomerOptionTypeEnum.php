<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Enumerations;

// TODO: Wait to see if sylius has something like this.
final class CustomerOptionTypeEnum implements EnumInterface
{
    const __default = null;

    const TEXT         = 'text';
    const SELECT       = 'select';
    const MULTI_SELECT = 'multi_select';
    const FILE         = 'file';
    const DATE         = 'date';
    const DATETIME     = 'datetime';
    const NUMBER       = 'number';
    const BOOLEAN      = 'boolean';

    public static function getConstList(): array
    {
        return [
            self::TEXT,
            self::SELECT,
            self::MULTI_SELECT,
            self::FILE,
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
}