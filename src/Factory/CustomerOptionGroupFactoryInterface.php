<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerOptionGroupFactoryInterface extends FactoryInterface
{
    /**
     * Creates an array of items based on the configuration
     */
    public function createFromConfig(array $configuration): CustomerOptionGroupInterface;

    /**
     * Generates a configuration for random items
     */
    public function generateRandom(int $amount): array;
}
