<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerOptionFactoryInterface extends FactoryInterface
{
    /**
     * @throws \Exception
     */
    public function validateConfiguration(array $configuration): void;

    /**
     * Creates an array of items based on the configuration
     */
    public function createFromConfig(array $configuration): CustomerOptionInterface;

    /**
     * Generates a configuration for random items
     */
    public function generateRandomConfiguration(int $amount): array;
}
