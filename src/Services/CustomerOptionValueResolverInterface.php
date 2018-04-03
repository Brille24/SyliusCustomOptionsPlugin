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
