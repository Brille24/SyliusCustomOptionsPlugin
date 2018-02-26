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
}