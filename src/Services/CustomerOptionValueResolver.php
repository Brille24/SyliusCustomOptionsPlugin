<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Services;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;

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
