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

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Symfony\Component\EventDispatcher\Event;


class OrderItemEvent extends Event
{
    /** @var OrderItemInterface */
    private $orderItem;

    public function __construct(OrderItemInterface $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    public function getOrderItem(): OrderItemInterface
    {
        return $this->orderItem;
    }
}
