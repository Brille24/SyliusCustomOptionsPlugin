<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints;

use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionValuePriceHasCustomerOptionValidator;
use Symfony\Component\Validator\Constraint;

class CustomerOptionValuePriceHasCustomerOptionConstraint extends Constraint
{
    /** @var string */
    public $message = 'brille24.validation.customer_option_value_price_does_not_have_customer_option';

    public function validatedBy()
    {
        return CustomerOptionValuePriceHasCustomerOptionValidator::class;
    }

    public function getTargets()
    {
        return [Constraint::CLASS_CONSTRAINT];
    }
}
