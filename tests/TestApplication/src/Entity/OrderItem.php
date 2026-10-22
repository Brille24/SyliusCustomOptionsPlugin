<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Traits\OrderItemCustomerOptionCapableTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_order_item')]
class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    use OrderItemCustomerOptionCapableTrait {
        __construct as protected customerOptionCapableConstructor;
    }

    public function __construct()
    {
        parent::__construct();
        $this->customerOptionCapableConstructor();
    }
}
