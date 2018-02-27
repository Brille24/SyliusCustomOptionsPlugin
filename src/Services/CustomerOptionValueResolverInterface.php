<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Services;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Exception;

interface CustomerOptionValueResolverInterface
{
    /**
     * Resolves the CustomerOptionValue (by code) of a CustomerOption. It throws an error if the CustomerOption is not
     * of type select and therefore has no defined values.
     *
     * @param CustomerOptionInterface $customerOption
     * @param string                  $value
     *
     * @return CustomerOptionValueInterface|null
     *
     * @throws Exception
     */
    public function resolve(CustomerOptionInterface $customerOption, string $value): ?CustomerOptionValueInterface;
}
