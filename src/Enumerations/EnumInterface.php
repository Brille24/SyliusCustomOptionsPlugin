<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Enumerations;


interface EnumInterface
{

    /**
     * Get a list of all available constants for the enumeration
     *
     * @return string[]
     */
    public static function getConstList(): array;

    /**
     * Checks if a value is a valid Enum Value
     *
     * @param $value
     *
     * @return bool
     */
    public static function isValid($value): bool;
}