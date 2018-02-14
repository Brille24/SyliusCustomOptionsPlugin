<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class CustomerOptionConfigurationConstraint extends Constraint
{
    public $message= '';

    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return CustomerOptionConfigurationConstraintValidator::class;
    }
}