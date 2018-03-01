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

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionableTraitInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface, CustomerOptionableTraitInterface
{
    /**
     * @return Collection|null
     */
    public function getCustomerOptionValuePrices(): ?Collection;

    /**
     * @param Collection|null $prices
     */
    public function setCustomerOptionValuePrices(?Collection $prices);
}
