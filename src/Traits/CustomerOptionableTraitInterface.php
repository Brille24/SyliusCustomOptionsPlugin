<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Traits;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;

interface CustomerOptionableTraitInterface
{
    /**
     * @return CustomerOptionGroupInterface|null
     */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface;

    /**
     * @param CustomerOptionGroupInterface|null $customerOptionGroup
     */
    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void;

    /**
     * Returns all customer options and an empty array if there are none associated
     *
     * @return CustomerOptionInterface[]
     */
    public function getCustomerOptions(): array;
}