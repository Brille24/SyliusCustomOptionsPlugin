<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionGroupTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct
{
    use CustomerOptionGroupTrait {
        __construct as protected initializeCustomerOptionGroup;
    }

    public function __construct()
    {
        parent::__construct();
        $this->initializeCustomerOptionGroup();
    }
}