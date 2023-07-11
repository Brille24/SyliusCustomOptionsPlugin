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

use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RemoveCustomerOptionFromOrderEvent extends Event
{
    public function __construct(private OrderInterface $order)
    {
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }
}
