<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints;

use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionValuePriceHasCustomerOptionValidator;
use Symfony\Component\Validator\Constraint;

class CustomerOptionValuePriceHasCustomerOptionConstraint extends Constraint
{
    /** @var string */
    public $message = 'brille24.validation.customer_option_value_price_does_not_have_customer_option';

    /** {@inheritdoc} */
    public function validatedBy(): string
    {
        return CustomerOptionValuePriceHasCustomerOptionValidator::class;
    }

    /** {@inheritdoc} */
    public function getTargets(): array
    {
        return [Constraint::CLASS_CONSTRAINT];
    }
}
