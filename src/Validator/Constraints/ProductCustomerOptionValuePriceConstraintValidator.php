<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 20.02.18
 * Time: 12:34
 */

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductCustomerOptionValuePriceConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if(!is_a($value, Collection::class)){
            throw new \InvalidArgumentException('Value is not a Collection.');
        }

        /** @var Collection $value */
        if($value->isEmpty()){
            return;
        }

        if(!is_a($value[0], CustomerOptionValuePriceInterface::class)){
            throw new \InvalidArgumentException('Collection does not contain CustomerOptionValuePrices.');
        }

        $existingTypes = [];

        /** @var CustomerOptionValuePriceInterface $price */
        foreach ($value->getValues() as $price){
            if(in_array($price->getCustomerOptionValue(), $existingTypes)){
                $this->context->addViolation($constraint->message);
            }else{
                $existingTypes[] = $price->getCustomerOptionValue();
            }
        }
    }
}