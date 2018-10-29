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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ProductCustomerOptionValuePriceConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $collection The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($collection, Constraint $constraint): void
    {
        Assert::isInstanceOf($collection, Collection::class);
        Assert::allObject($collection);
        Assert::allImplementsInterface($collection, CustomerOptionValuePriceInterface::class);

        /** @var Collection $collection */
        if ($collection->isEmpty()) {
            return;
        }

        $existingValues = [];
        foreach ($collection->toArray() as $price) {
            /** @var CustomerOptionValuePriceInterface $price */
            $priceChannel = $price->getChannel();
            if ($priceChannel === null) {
                continue;
            }

            $channelCode = $priceChannel->getCode();

            if (!isset($existingValues[$channelCode])) {
                $existingValues[$channelCode] = [];
            }

            if (in_array($price->getCustomerOptionValue(), $existingValues[$channelCode], true)) {
                $this->context->addViolation('');
            } else {
                $existingValues[$channelCode][] = $price->getCustomerOptionValue();
            }
        }
    }
}
