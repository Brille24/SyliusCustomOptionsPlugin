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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Traits\OrderItemCustomerOptionCapableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItem as SyliusOrderItem;
use Sylius\Component\Core\Model\OrderItemInterface as BaseCoreOrderItemInterface;
use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;

class OrderItem extends SyliusOrderItem implements OrderItemInterface
{
    use OrderItemCustomerOptionCapableTrait {
        __construct as customerOptionCapableConstructor;
    }

    public function __construct()
    {
        parent::__construct();

        $this->customerOptionCapableConstructor();
    }
}
