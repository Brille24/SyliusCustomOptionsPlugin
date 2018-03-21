<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Exceptions\ConfigurationException;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Faker\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class CustomerOptionFactory implements CustomerOptionFactoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $customerOptionGroupRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var CustomerOptionValueFactoryInterface
     */
    private $customerOptionValueFactory;

    public function __construct(
        CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository,
        CustomerOptionValueFactoryInterface $customerOptionValueFactory
    ) {
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->customerOptionValueFactory = $customerOptionValueFactory;

        $this->faker = Factory::create();
    }

    /**
     * Validates the option array and throws an error if it is not valid
     *
     * @param array $configuration
     *
     * @throws \Exception
     */
    public function validateConfiguration(array $configuration): void
    {
        ConfigurationException::createFromMissingArrayKey('code', $configuration);

        ConfigurationException::createFromMissingArrayKey('translations', $configuration);
        if (!is_array($configuration['translations'])) {
            throw new ConfigurationException('The translations have to be an array');
        }
        ConfigurationException::createFromMinimumLength(1, $configuration['translations']);

        ConfigurationException::createFromMissingArrayKey('type', $configuration);
        ConfigurationException::createFromArrayContains($configuration['type'], CustomerOptionTypeEnum::getConstList());
        if (CustomerOptionTypeEnum::isSelect($configuration['type'])) {
            ConfigurationException::createFromMissingArrayKey('values', $configuration);
        }

        ConfigurationException::createFromMissingArrayKey('groups', $configuration);
    }

    /**
     * @param array $configuration
     *
     * @return CustomerOptionInterface
     *
     * @throws \Exception
     */
    public function createFromConfig(array $configuration): CustomerOptionInterface
    {
        $this->validateConfiguration($configuration);

        $customerOption = new CustomerOption();
        $customerOption->setCode($configuration['code']);

        foreach ($configuration['translations'] as $locale => $name) {
            $customerOption->setCurrentLocale($locale);
            $customerOption->setName($name);
        }

        $customerOption->setType($configuration['type']);

        if (CustomerOptionTypeEnum::isSelect($configuration['type'])) {
            foreach ($configuration['values'] as $valueConfig) {
                /** @var CustomerOptionValueInterface $value */
                $value = $this->customerOptionValueFactory->createFromConfig($valueConfig);

                $customerOption->addValue($value);
            }
        }

        $customerOption->setRequired(isset($configuration['required']) ? (bool) ($configuration['required']) : false);

        foreach ($configuration['groups'] as $groupCode) {
            /** @var CustomerOptionGroupInterface $group */
            $group = $this->customerOptionGroupRepository->findOneByCode($groupCode);

            if ($group !== null) {
                $groupAssoc = new CustomerOptionAssociation();

                $group->addOptionAssociation($groupAssoc);
                $customerOption->addGroupAssociation($groupAssoc);
            }
        }

        return $customerOption;
    }

    /**
     * Generates a random number of CustomerOptions
     *
     * @param int $amount
     *
     * @return CustomerOptionInterface[]
     */
    public function generateRandomConfiguration(int $amount): array
    {
        $types = CustomerOptionTypeEnum::getConstList();

        $this->faker->unique($reset = true);

        $result = [];
        for ($i = 0; $i < $amount; ++$i) {
            $options = [];

            $options['code'] = $this->faker->uuid;
            $options['translations']['en_US'] = sprintf('CustomerOption "%s"', $this->faker->unique()->word);
            $options['type'] = $this->faker->randomElement($types);
            $options['required'] = $this->faker->boolean;

            if (CustomerOptionTypeEnum::isSelect($options['type'])) {
                $numValues = $this->faker->numberBetween(2, 4);
                $options['values'] = $this->customerOptionValueFactory->generateRandomConfiguration($numValues);
            }

            /** @var CustomerOptionGroupInterface[] $groups */
            $groups = $this->customerOptionGroupRepository->findAll();
            $groupCodes = array_map(function (CustomerOptionGroupInterface $group) { return $group->getCode(); },
                $groups);

            $options['groups'] = [];
            if (count($groupCodes) > 0) {
                $options['groups'] = $this->faker->randomElements($groupCodes);
            }
            $result[] = $options;
        }

        return $result;
    }

    public function createNew()
    {
        return new CustomerOption();
    }
}
