<?php

declare(strict_types=1);

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionValuePriceHasCustomerOptionConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class CustomerOptionValuePriceHasCustomerOptionValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $product The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($product, Constraint $constraint): void
    {
        Assert::isInstanceOf(
            $product,
            ProductInterface::class,
            sprintf('$product is not type of %s', ProductInterface::class),
        );
        Assert::isInstanceOf(
            $constraint,
            CustomerOptionValuePriceHasCustomerOptionConstraint::class,
            sprintf('$constraint is not type of %s', CustomerOptionValuePriceHasCustomerOptionConstraint::class),
        );

        $customerOptions = $product->getCustomerOptions();
        /** @var CustomerOptionValuePriceInterface $valuePrice */
        foreach ($product->getCustomerOptionValuePrices() as $valuePrice) {
            /** @var CustomerOptionValueInterface $customerOptionValue */
            $customerOptionValue = $valuePrice->getCustomerOptionValue();

            /** @var CustomerOptionInterface $customerOption */
            $customerOption = $customerOptionValue->getCustomerOption();
            if (!in_array($customerOption, $customerOptions, true)) {
                // The product shouldn't have this price
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($customerOptionValue->getCode() ?? '')
                    ->setCause($customerOption->getCode())
                    ->addViolation()
                ;
            }
        }
    }
}
