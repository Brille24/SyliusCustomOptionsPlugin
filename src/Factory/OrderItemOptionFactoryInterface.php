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

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;

interface OrderItemOptionFactoryInterface
{
    /**
     * @param mixed                   $customerOptionValue
     */
    public function createForOptionAndValue(
        OrderItemInterface $orderItem,
        CustomerOptionInterface $customerOption,
        $customerOptionValue,
    ): OrderItemOptionInterface;

    /**
     * Creates an OrderItemOption based on the two input strings
     *
     * @param string              $customerOptionCode The code of teh customer option
     * @param mixed               $customerOptionValue The code of the value if it is a select else just the value itself
     */
    public function createNewFromStrings(
        OrderItemInterface $orderItem,
        string $customerOptionCode,
        $customerOptionValue,
    ): OrderItemOptionInterface;
}
