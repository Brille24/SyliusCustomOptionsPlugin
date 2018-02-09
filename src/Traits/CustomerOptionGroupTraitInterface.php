<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Traits;



use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;

interface CustomerOptionGroupTraitInterface
{
    /**
     * @return CustomerOptionGroup|null
     */
    public function getCustomerOptionGroup(): ?CustomerOptionGroup;

    /**
     * @param CustomerOptionGroup|null $customerOptionGroup
     */
    public function setCustomerOptionGroup(?CustomerOptionGroup $customerOptionGroup): void;
}