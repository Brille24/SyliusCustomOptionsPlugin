<?php

declare(strict_types=1);
/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConfigurationException;
use Faker\Factory;
use Faker\Generator;

class CustomerOptionValueFactory implements CustomerOptionValueFactoryInterface
{
    /**
     * @var CustomerOptionValuePriceFactoryInterface
     */
    private $valuePriceFactory;

    /**
     * @var Generator
     */
    private $faker;

    public function __construct(CustomerOptionValuePriceFactoryInterface $valuePriceFactory)
    {
        $this->valuePriceFactory = $valuePriceFactory;
        $this->faker             = Factory::create();
    }

    /**
     * @param array $configuration
     *
     * @throws ConfigurationException
     */
    public function validateConfiguration(array $configuration): void
    {
        ConfigurationException::createFromMissingArrayKey('code', $configuration);

        ConfigurationException::createFromMissingArrayKey('translations', $configuration);
        if (!is_array($configuration['translations'])) {
            throw new ConfigurationException('The translations have to be an array');
        }
        ConfigurationException::createFromMinimumLength(1, $configuration['translations']);

        ConfigurationException::createFromMissingArrayKey('prices', $configuration);
        if (!is_array($configuration['prices'])) {
            throw new ConfigurationException('The translations have to be an array');
        }
    }

    /** {@inheritdoc} */
    public function createFromConfig(array $configuration): CustomerOptionValueInterface
    {
        $value = new CustomerOptionValue();
        $value->setCode($configuration['code']);

        foreach ($configuration['translations'] as $locale => $name) {
            $value->setCurrentLocale($locale);
            $value->setName($name);
        }

        // Creating prices
        foreach ($configuration['prices'] as $priceConfig) {
            $price = $this->valuePriceFactory->createFromConfig($priceConfig);

            $value->addPrice($price);
        }

        return $value;
    }

    /** {@inheritdoc} */
    public function generateRandomConfiguration(int $amount): array
    {
        $result = [];
        $this->faker->unique($reset = true);

        for ($j = 0; $j < $amount; ++$j) {
            $priceAmount = $this->faker->numberBetween(0, 2);

            $config = [
                'code'         => $this->faker->uuid,
                'translations' => ['en_US' => sprintf('Value "%s"', $this->faker->word)],
                'prices'       => $this->valuePriceFactory->generateRandomConfiguration($priceAmount),
            ];

            $result[] = $config;
        }

        return $result;
    }

    public function createNew(): CustomerOptionValueInterface
    {
        return new CustomerOptionValue();
    }
}
