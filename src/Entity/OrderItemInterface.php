<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface
{
    /**
     * @param OrderItemOptionInterface[] $customerOptionConfiguration
     */
    public function setCustomerOptionConfiguration(array $customerOptionConfiguration): void;

    /**
     * @return OrderItemOptionInterface[]
     */
    public function getCustomerOptionConfiguration(): array;
}
