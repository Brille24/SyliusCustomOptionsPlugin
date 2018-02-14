<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface
{
    public function getCustomerOptionGroup(): ?CustomerOptionGroup;

    public function setCustomerOptionGroup(?CustomerOptionGroup $customerOptionGroup);

    public function getCustomerOptionPrices(): Collection;

    public function setCustomerOptionPrices(Collection $prices);

    public function getCustomerOptions(): Collection;
}