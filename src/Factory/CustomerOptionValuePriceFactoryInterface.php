<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerOptionValuePriceFactoryInterface extends FactoryInterface
{
    /**
     * @param array $configuration
     *
     * @throws \Exception
     */
    public function validateConfiguration(array $configuration): void;

    /**
     * Creates an array of items based on the configuration
     *
     * @param array $configuration
     *
     * @return CustomerOptionValuePriceInterface
     */
    public function createFromConfig(array $configuration): CustomerOptionValuePriceInterface;

    /**
     * Generates a configuration for random items
     *
     * @param int $amount
     *
     * @return array
     */
    public function generateRandomConfiguration(int $amount): array;
}
