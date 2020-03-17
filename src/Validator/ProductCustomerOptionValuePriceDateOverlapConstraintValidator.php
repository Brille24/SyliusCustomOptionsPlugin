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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\ProductCustomerOptionValuePriceDateOverlapConstraint;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ProductCustomerOptionValuePriceDateOverlapConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $valuePrices The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($valuePrices, Constraint $constraint): void
    {
        Assert::isInstanceOf($valuePrices, Collection::class);
        Assert::isInstanceOf($constraint, ProductCustomerOptionValuePriceDateOverlapConstraint::class);
        Assert::allObject($valuePrices);
        Assert::allImplementsInterface($valuePrices, CustomerOptionValuePriceInterface::class);

        /** @var Collection $collection */
        if ($valuePrices->isEmpty()) {
            return;
        }

        $existingPrices = [];
        foreach ($valuePrices->toArray() as $currentPrice) {
            /** @var CustomerOptionValuePriceInterface $currentPrice */
            $priceChannel = $currentPrice->getChannel();
            if ($priceChannel === null) {
                continue;
            }

            $channelCode = $priceChannel->getCode();

            /** @var CustomerOptionValueInterface $customerOptionValue */
            $customerOptionValue = $currentPrice->getCustomerOptionValue();
            $customerOptionValueCode = $customerOptionValue->getCode();

            /** @var CustomerOptionInterface $customerOption */
            $customerOption = $customerOptionValue->getCustomerOption();
            $customerOptionCode = $customerOption->getCode();

            if (!isset($existingPrices[$channelCode][$customerOptionCode][$customerOptionValueCode])) {
                $existingPrices[$channelCode][$customerOptionCode][$customerOptionValueCode] = [];
            }

            // Check if the date ranges intersect
            /** @var CustomerOptionValuePriceInterface $existingPrice */
            foreach ($existingPrices[$channelCode][$customerOptionCode][$customerOptionValueCode] as $existingPrice) {
                // Don't compare with itself
                if ($existingPrice === $currentPrice) {
                    continue;
                }

                $currentDateRange = $currentPrice->getDateValid();
                $existingDateRange = $existingPrice->getDateValid();

                if ($currentDateRange === $existingDateRange) {
                    // Either the two prices have the same DateRange object or both date ranges are null
                    $this->context->addViolation($constraint->message);
                }

                if (null !== $currentDateRange && null !== $existingDateRange) {
                    // Compare the date ranges for overlap
                    if (
                        $currentDateRange->contains($existingDateRange->getStart()) ||
                        $currentDateRange->contains($existingDateRange->getEnd()) ||
                        $existingDateRange->contains($currentDateRange->getStart()) ||
                        $existingDateRange->contains($currentDateRange->getEnd())
                    ) {
                        // The date ranges overlap
                        $this->context->addViolation($constraint->message);
                    }
                }
            }

            $existingPrices[$channelCode][$customerOptionCode][$customerOptionValueCode][] = $currentPrice;
        }
    }
}
