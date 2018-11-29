<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;

interface OrderItemCustomerOptionCapableTraitInterface
{
    /**
     * @param OrderItemOptionInterface[] $customerOptionConfiguration
     */
    public function setCustomerOptionConfiguration(array $customerOptionConfiguration): void;

    /**
     * @param bool $assoc If this is true it will create an associative array with the customer option code as key
     *
     * @return OrderItemOptionInterface[]
     */
    public function getCustomerOptionConfiguration(bool $assoc=false): array;

    /**
     * Returns the compressed version of the customer option configuration as though it was sent through a web-request.
     *
     * @return array
     */
    public function getCustomerOptionConfigurationAsSimpleArray(): array;
}
