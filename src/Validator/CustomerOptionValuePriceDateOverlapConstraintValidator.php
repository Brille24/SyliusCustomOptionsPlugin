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
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionValuePriceDateOverlapConstraint;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class CustomerOptionValuePriceDateOverlapConstraintValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $valuePrices The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($valuePrices, Constraint $constraint): void
    {
        Assert::isInstanceOf(
            $valuePrices,
            Collection::class,
            sprintf('$valuePrices is not type of %s', Collection::class)
        );
        Assert::isInstanceOf(
            $constraint,
            CustomerOptionValuePriceDateOverlapConstraint::class,
            sprintf('$constraint is not type of %s', CustomerOptionValuePriceDateOverlapConstraint::class)
        );
        Assert::allObject($valuePrices, '$valuePrices has non object');
        Assert::allImplementsInterface(
            $valuePrices,
            CustomerOptionValuePriceInterface::class,
            sprintf('$valuePrices has object not implementing %s', CustomerOptionValuePriceInterface::class)
        );

        /** @var Collection $valuePrices */
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
            $customerOptionValue     = $currentPrice->getCustomerOptionValue();
            $customerOptionValueCode = $customerOptionValue->getCode() ?? '';

            /** @var CustomerOptionInterface $customerOption */
            $customerOption     = $customerOptionValue->getCustomerOption();
            $customerOptionCode = $customerOption->getCode() ?? '';

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

                $currentDateRange  = $currentPrice->getDateValid();
                $existingDateRange = $existingPrice->getDateValid();

                if ($currentDateRange === $existingDateRange) {
                    // Either the two prices have the same DateRange object or both date ranges are null
                    $this->context
                        ->buildViolation($constraint->message)
                        ->atPath($customerOptionValueCode)
                        ->setInvalidValue($currentDateRange)
                        ->setCause($existingDateRange)
                        ->addViolation()
                    ;
                }

                // Compare the date ranges for overlap
                if (null !== $currentDateRange && null !== $existingDateRange) {
                    $dateOverLaps = $currentDateRange->contains($existingDateRange->getStart()) ||
                    $currentDateRange->contains($existingDateRange->getEnd()) ||
                    $existingDateRange->contains($currentDateRange->getStart()) ||
                    $existingDateRange->contains($currentDateRange->getEnd());

                    if ($dateOverLaps) {
                        $this->context
                            ->buildViolation($constraint->message)
                            ->atPath($customerOptionValueCode)
                            ->setInvalidValue($currentDateRange)
                            ->setCause($existingDateRange)
                            ->addViolation()
                        ;
                    }
                }
            }

            $existingPrices[$channelCode][$customerOptionCode][$customerOptionValueCode][] = $currentPrice;
        }
    }
}
