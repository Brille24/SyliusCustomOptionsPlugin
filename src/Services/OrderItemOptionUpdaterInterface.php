<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;

interface OrderItemOptionUpdaterInterface
{
    /**
     * Updates the order item options based on a plain string array
     *
     * @param OrderItemInterface   $orderItem
     * @param array<string, mixed> $data
     *     Associative array of key value pairs for the new array.
     *     The key is the custom option code and the value is the new value.
     */
    public function updateOrderItemOptions(OrderItemInterface $orderItem, array $data): void;
}
