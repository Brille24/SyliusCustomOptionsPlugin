<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;
use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionableTraitInterface;

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
