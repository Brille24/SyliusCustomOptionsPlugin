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
use Symfony\Component\EventDispatcher\Event;


class OrderItemOptionEvent extends Event
{
    /** @var OrderItemOptionInterface */
    private $orderItemOption;

    public function __construct(OrderItemOptionInterface $orderItemOption)
    {
        $this->orderItemOption = $orderItemOption;
    }

    public function getOrderItemOption(): OrderItemOptionInterface
    {
        return $this->orderItemOption;
    }
}
