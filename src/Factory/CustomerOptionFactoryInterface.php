<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;


interface CustomerOptionFactoryInterface
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
    public function create(array $configuration);

    /**
     * Generates a configuration for random items
     *
     * @param int $amount
     *
     * @return array
     */
    public function generateRandomConfiguration(int $amount): array;
}