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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
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
        if (!$collection instanceof Collection) {
            throw new InvalidArgumentException('Value is not a Collection.');
        }

        /** @var Collection $collection */
        if ($collection->isEmpty()) {
            return;
        }

        if (!$collection->first() instanceof CustomerOptionValuePriceInterface) {
            throw new InvalidArgumentException('Collection does not contain CustomerOptionValuePrices.');
        }

        $existingValues = [];

        /** @var CustomerOptionValuePriceInterface $price */
        foreach ($collection as $price) {
            $priceChannel = $price->getChannel();
            if($priceChannel === null){
                continue;
            }

            $channelCode = $priceChannel->getCode();

            if (!isset($existingValues[$channelCode])) {
                $existingValues[$channelCode] = [];
            }

            if (in_array($price->getCustomerOptionValue(), $existingValues[$channelCode])) {
                $this->context->addViolation('');
            } else {
                $existingValues[$channelCode][] = $price->getCustomerOptionValue();
            }
        }
    }
}
