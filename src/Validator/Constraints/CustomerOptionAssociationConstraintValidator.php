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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomerOptionAssociationConstraintValidator extends ConstraintValidator
{
    /** {@inheritdoc} */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof Collection) {
            throw new \InvalidArgumentException(get_class($this) . ' can only validate collections containing ' . CustomerOptionAssociationInterface::class);
        }

        $optionsSoFar = [];
        foreach ($value->toArray() as $uniqueValue) {
            /** @var CustomerOptionAssociationInterface $uniqueValue */
            $this->checkElementType($uniqueValue);

            $customerOption = $uniqueValue->getOption();
            if (in_array($customerOption, $optionsSoFar)) {
                $this->context->addViolation('sylius.ui.code');
            } else {
                $optionsSoFar[] = $customerOption;
            }
        }
    }

    private function checkElementType($element): void
    {
        if (!$element instanceof CustomerOptionAssociationInterface) {
            throw new \InvalidArgumentException('Invalid entry type');
        }
    }
}
