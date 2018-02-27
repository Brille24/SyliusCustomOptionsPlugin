<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 20.02.18
 * Time: 12:34
 */
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Validator\Constraints;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ProductCustomerOptionValuePriceConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($collection, Constraint $constraint): void
    {
        if (!is_a($collection, Collection::class)) {
            throw new InvalidArgumentException('Value is not a Collection.');
        }

        /** @var Collection $collection */
        if ($collection->isEmpty()) {
            return;
        }

        if (!is_a($collection[0], CustomerOptionValuePriceInterface::class)) {
            throw new InvalidArgumentException('Collection does not contain CustomerOptionValuePrices.');
        }

        $existingValues = [];

        /** @var CustomerOptionValuePriceInterface $price */
        foreach ($collection as $price) {
            $channelCode = $price->getChannel()->getCode();

            if (!isset($existingValues[$channelCode])) {
                $existingValues[$channelCode] = [];
            }

            if (in_array($price->getCustomerOptionValue(), $existingValues[$channelCode])) {
                $this->context->addViolation($constraint->message);
            } else {
                $existingValues[$channelCode][] = $price->getCustomerOptionValue();
            }
        }
    }
}