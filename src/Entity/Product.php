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

use Brille24\SyliusCustomerOptionsPlugin\Traits\ProductCustomerOptionCapableTrait;
use Sylius\Component\Core\Model\Product as SyliusProduct;

class Product extends SyliusProduct implements ProductInterface
{
    use ProductCustomerOptionCapableTrait {
        __construct as protected customerOptionCapableConstructor;
    }

    public function __construct()
    {
        parent::__construct();

        $this->customerOptionCapableConstructor();
    }
}
