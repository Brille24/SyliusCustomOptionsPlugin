<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;

interface OrderItemOptionFactoryInterface
{
    /**
     * @return OrderItemOptionInterface
     */
    public function createNew(CustomerOptionInterface $customerOption, $customerOptionValue): OrderItemOptionInterface;

    /**
     * Creates an OrderItemOption based on the two input strings
     *
     * @param string $customerOptionCode The code of teh customer option
     * @param string $customerOptionValue The code of the value if it is a select else just the value itself
     *
     * @return OrderItemOptionInterface
     */
    public function createNewFromStrings(
        string $customerOptionCode,
        string $customerOptionValue
    ): OrderItemOptionInterface;
}