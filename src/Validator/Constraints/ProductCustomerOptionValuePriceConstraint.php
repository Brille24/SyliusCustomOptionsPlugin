<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 20.02.18
 * Time: 12:33
 */

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class ProductCustomerOptionValuePriceConstraint extends Constraint
{
    public $message = '';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return ProductCustomerOptionValuePriceConstraintValidator::class;
    }
}