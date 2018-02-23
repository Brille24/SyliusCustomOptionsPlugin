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



//        $existingValues = [];
//
//        /** @var CustomerOptionValuePriceInterface $price */
//        foreach ($value->getValues() as $price){
//            if($existingValues[$price->getChannel()->getCode()] === $price->getCustomerOptionValue()){
//                $this->context->addViolation($constraint->message);
//            }else{
//                $existingValues[$price->getChannel()->getCode()] = $price->getCustomerOptionValue();
//            }
//        }

        $existingValues = [];

        /** @var CustomerOptionValuePriceInterface $price */
        foreach($value->getValues() as $price){
            $channelCode = $price->getChannel()->getCode();

            if(
                !isset($existingValues[$channelCode])
            ){
                $existingValues[$channelCode] = [];
            }

            if(in_array($price->getCustomerOptionValue(), $existingValues[$channelCode])){
                $this->context->addViolation($constraint->message);
            }else{
                $existingValues[$channelCode][] = $price->getCustomerOptionValue();
            }
        }
    }
}