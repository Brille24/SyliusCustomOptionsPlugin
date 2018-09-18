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

use Brille24\SyliusCustomerOptionsPlugin\Traits\ProductCustomerOptionCapableTraitInterface;
use Sylius\Component\Core\Model\ProductInterface as SyliusProductInterface;

interface ProductInterface extends SyliusProductInterface, ProductCustomerOptionCapableTraitInterface
{
}
