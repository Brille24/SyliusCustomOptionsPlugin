<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerOptionValuePriceFactoryInterface extends FactoryInterface
{
    /**
     * @deprecated
     * This function will be removed in future versions and replaced with an OptionResolver
     *
     * @throws \Exception
     */
    public function validateConfiguration(array $configuration): void;

    /**
     * Creates an array of items based on the configuration
     */
    public function createFromConfig(array $configuration): CustomerOptionValuePriceInterface;

    /**
     * Generates a configuration for random items
     */
    public function generateRandomConfiguration(int $amount): array;
}
