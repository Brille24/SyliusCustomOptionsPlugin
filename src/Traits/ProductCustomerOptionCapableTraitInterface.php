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

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\Common\Collections\Collection;

interface ProductCustomerOptionCapableTraitInterface
{
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface;

    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void;

    /**
     * Returns all customer options and an empty array if there are none associated
     *
     * @return CustomerOptionInterface[]
     */
    public function getCustomerOptions(): array;

    public function hasCustomerOptions(): bool;

    /**
     * @return Collection<CustomerOptionValuePriceInterface>
     */
    public function getCustomerOptionValuePrices(): Collection;

    /**
     * @param Collection<CustomerOptionValuePriceInterface>|null $prices
     */
    public function setCustomerOptionValuePrices(?Collection $prices): void;

    public function addCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void;

    public function removeCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void;
}
