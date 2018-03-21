<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;


use Sylius\Component\Resource\Factory\FactoryInterface;

interface CustomerOptionValueFactoryInterface extends FactoryInterface
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
     */
    public function createFromConfig(array $configuration);

    /**
     * Generates a configuration for random items
     *
     * @param int $amount
     *
     * @return array
     */
    public function generateRandomConfiguration(int $amount): array;
}