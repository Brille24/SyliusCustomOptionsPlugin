<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;

interface OrderItemValueCopierInterface
{
    /**
     * @param OrderInterface $order
     */
    public function copyOverValues(OrderInterface $order): void;

    /**
     * @param OrderItemInterface $order
     */
    public function copyOverValuesForOrderItem(OrderItemInterface $order): void;
}
