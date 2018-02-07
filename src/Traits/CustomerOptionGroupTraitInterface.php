<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Traits;


use Brille24\CustomerOptionsBundle\Entity\CustomerOptionGroup;

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