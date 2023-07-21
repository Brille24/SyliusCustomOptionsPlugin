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

namespace Brille24\SyliusCustomerOptionsPlugin\Event;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RecalculateOrderItemOptionEvent extends Event
{
    public function __construct(private OrderItemOptionInterface $orderItemOption)
    {
    }

    public function getOrderItemOption(): OrderItemOptionInterface
    {
        return $this->orderItemOption;
    }
}
