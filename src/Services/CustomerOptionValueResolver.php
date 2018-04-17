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

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;

/**
 * Class CustomerOptionValueResolver
 *
 * This class is used to turn a value string of a Customer Option with Values into the value object
 */
final class CustomerOptionValueResolver implements CustomerOptionValueResolverInterface
{
    /** {@inheritdoc} */
    public function resolve(CustomerOptionInterface $customerOption, string $value): ?CustomerOptionValueInterface
    {
        if (!CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
            throw new \Exception('Can not resolve non-select values');
        }

        foreach ($customerOption->getValues() as $valueObject) {
            if ($valueObject->getCustomerOption() === $customerOption && $valueObject->getCode() === $value) {
                return $valueObject;
            }
        }

        return null;
    }
}
