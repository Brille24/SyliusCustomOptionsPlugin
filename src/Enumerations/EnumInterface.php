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

namespace Brille24\SyliusCustomerOptionsPlugin\Enumerations;

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
     * @param mixed $value
     *
     * @return bool
     */
    public static function isValid($value): bool;

    /**
     * Returns an associative array with the values as key and the labels as values
     *
     * @return array
     */
    public static function getTranslateArray(): array;

//    public static function getFormTypeArray(): array;
}
