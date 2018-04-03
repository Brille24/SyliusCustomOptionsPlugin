<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class ConditionalConstraint extends Constraint
{
    public $conditions;

    public $constraints;

    public $message = 'One or more constraints not met.';

    public function __construct($options = null)
    {
        if ($options !== null && !is_array($options)) {
            $options = [
                'conditions' => [],
                'constraints' => [],
            ];
        }

        parent::__construct($options);

        if ($this->conditions === null || $this->constraints === null) {
            throw new MissingOptionsException('No conditions or constraints given.', ['conditions', 'constraints']);
        }
    }

    public function validatedBy()
    {
        return ConditionalConstraintValidator::class;
    }
}
