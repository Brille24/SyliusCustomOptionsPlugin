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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class CustomerOptionAssociationConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, Collection::class);

        $optionsSoFar = [];
        foreach ($value->toArray() as $uniqueValue) {
            Assert::isInstanceOf($uniqueValue, CustomerOptionAssociationInterface::class);

            /** @var CustomerOptionAssociationInterface $uniqueValue */
            $customerOption = $uniqueValue->getOption();
            if (in_array($customerOption, $optionsSoFar, true)) {
                $this->context->addViolation('sylius.ui.code');
            } else {
                $optionsSoFar[] = $customerOption;
            }
        }
    }
}
