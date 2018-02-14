<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionableTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ProductInterface
{
    use CustomerOptionableTrait {
        __construct as protected initializeCustomerOptionGroup;
    }

    public function __construct()
    {
        parent::__construct();
        $this->initializeCustomerOptionGroup();
    }
}