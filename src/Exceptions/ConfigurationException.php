<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Exceptions;


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
    public static function createFromMissingArrayKey(string $key, array $array)
    {
        if (!array_key_exists($key, $array)) {
            throw new ConfigurationException('The configuration does not contain key: "' . $key . '"');
        }
    }

    /**
     * Checks if the array contains the first value and if not it throws an exception
     *
     * @param       $element
     * @param array $array
     *
     * @throws ConfigurationException
     */
    public static function createFromArrayContains($element, array $array)
    {
        if (!in_array($element, $array)) {
            $arrayString = join(',', $array);
            throw new ConfigurationException("'$element' should be in array $arrayString");
        }
    }

    public static function createFromMinimumLength(int $minLength, array $array)
    {
        if(count($array) < $minLength){
            throw new ConfigurationException("The array has to be at least $minLength element(s) long");
        }
    }
}