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

namespace Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionConfigurationConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class CustomerOptionConfigurationConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isArray($value);
        Assert::isInstanceOf($constraint, CustomerOptionConfigurationConstraint::class);
        /** @var CustomerOptionConfigurationConstraint $constraint */
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Can not validate configurations.');
        }

        /**
         * The array can look like this
         * filesize.min => 2
         * filesize.max => 10
         *
         * So in this line we look for the array key that contains min and max
         */
        $minKeys = array_filter(array_keys($value), function (string $key) {
            return is_int(strpos($key, 'min'));
        });
        $maxKeys = array_filter(array_keys($value), function (string $key) {
            return is_int(strpos($key, 'max'));
        });

        if (count($minKeys) === 0 || count($maxKeys) === 0) {
            return;
        }

        // Get the value by getting the first element with the min and max keyword respectively
        $minValue = $value[reset($minKeys)]['value'];
        $maxValue = $value[reset($maxKeys)]['value'];

        if ($minValue > $maxValue) {
            $this->context->addViolation($constraint->message);
        }
    }
}
