<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Exceptions;

class ConfigurationException extends \Exception
{
    /**
     * Checks if the array has the key, if not it throws an exception
     *
     * @param string $key
     * @param array  $array
     *
     * @throws ConfigurationException
     */
    public static function createFromMissingArrayKey(string $key, array $array): void
    {
        if (!array_key_exists($key, $array)) {
            throw new self('The configuration does not contain key: "'.$key.'"');
        }
    }

    /**
     * Checks if the array contains the first value and if not it throws an exception
     *
     * @param mixed $element
     * @param array $array
     *
     * @throws ConfigurationException
     */
    public static function createFromArrayContains($element, array $array): void
    {
        if (!in_array($element, $array, true)) {
            $arrayString = implode(',', $array);

            throw new self("'$element' should be in array $arrayString");
        }
    }

    public static function createFromMinimumLength(int $minLength, array $array): void
    {
        if (count($array) < $minLength) {
            throw new self("The array has to be at least $minLength element(s) long");
        }
    }
}
