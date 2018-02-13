<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CustomerOptionAssociationConstraintValidator extends ConstraintValidator
{

    /** {@inheritdoc} */
    public function validate($value, Constraint $constraint)
    {

        if (!$value instanceof Collection) {
            throw new \InvalidArgumentException(get_class($this) . ' can only validate collections containing ' . CustomerOptionAssociationInterface::class);
        }

        $optionsSoFar = [];

        foreach ($value->toArray() as $uniqueValue) {
            /** @var CustomerOptionAssociationInterface $uniqueValue */
            $this->checkElementType($uniqueValue);

            if (in_array($uniqueValue->getOption(), $optionsSoFar)) {
                $this->context->addViolation($constraint->message);
            } else {
                $optionsSoFar[] = $uniqueValue->getOption();
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