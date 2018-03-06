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

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomerOptionConfigurationConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Can not validate configurations.');
        }

        // Get the array key that contains min and max
        $minKeys = array_filter(array_keys($value), function (string $key) { return is_int(strpos($key, 'min')); });
        $maxKeys = array_filter(array_keys($value), function (string $key) { return is_int(strpos($key, 'max')); });

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
