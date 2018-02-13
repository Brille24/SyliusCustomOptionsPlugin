<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class CustomerOptionAssociationConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.ui.code';

    /** {@inheritdoc} */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /** {@inheritdoc} */
    public function validatedBy()
    {
        return CustomerOptionAssociationConstraintValidator::class;
    }
}