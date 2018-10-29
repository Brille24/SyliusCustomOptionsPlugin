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

namespace Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints;

use Brille24\SyliusCustomerOptionsPlugin\Validator\ProductCustomerOptionValuePriceConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ProductCustomerOptionValuePriceConstraint extends Constraint
{
    /** @var string */
    public $message = '';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return ProductCustomerOptionValuePriceConstraintValidator::class;
    }
}
