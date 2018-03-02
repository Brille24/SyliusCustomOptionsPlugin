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

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\{
    CustomerOption, CustomerOptionAssociation, CustomerOptionGroupInterface, CustomerOptionValue, CustomerOptionValuePrice
};
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class CustomerOptionFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RepositoryInterface
     */
    private $customerOptionGroupRepository;

    /**
     * @var EntityRepository
     */
    private $channelRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    public function __construct(
        EntityManagerInterface $em,
        CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->em = $em;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->channelRepository = $channelRepository;

        $this->faker = Factory::create();
    }

    /**
     * @param array $options
     *
     * @throws \Exception
     */
    public function create(array $options = [])
    {
        $options = array_merge($this->getOptionsPrototype(), $options);

        $customerOption = new CustomerOption();

        $customerOption->setCode($options['code']);

        foreach ($options['translations'] as $locale => $name) {
            $customerOption->setCurrentLocale($locale);
            $customerOption->setName($name);
        }
        $customerOption->setType($options['type']);

        if(CustomerOptionTypeEnum::isSelect($options['type'])) {

            foreach ($options['values'] as $valueConfig) {
                $value = new CustomerOptionValue();
                $value->setCode($valueConfig['code']);

                foreach ($valueConfig['translations'] as $locale => $name) {
                    $value->setCurrentLocale($locale);
                    $value->setName($name);
                }

                $prices = new ArrayCollection();

                foreach ($valueConfig['prices'] as $priceConfig) {
                    $price = new CustomerOptionValuePrice();

                    if ($priceConfig['type'] === 'fixed') {
                        $price->setType(CustomerOptionValuePrice::TYPE_FIXED_AMOUNT);
                    } elseif ($priceConfig['type'] === 'percent') {
                        $price->setType(CustomerOptionValuePrice::TYPE_PERCENT);
                    } else {
                        throw new \Exception(sprintf("Value price type '%s' does not exist!", $priceConfig['type']));
                    }

                    $price->setAmount($priceConfig['amount']);
                    $price->setPercent($priceConfig['percent']);
                    $price->setCustomerOptionValue($value);

                    /** @var ChannelInterface $channel */
                    $channel = $this->channelRepository->findOneBy(['code' => $priceConfig['channel']]);

                    if ($channel === null) {
                        $channels = new ArrayCollection($this->channelRepository->findAll());
                        $channel = $channels->first() ? $channels->first() : null;
                    }

                    $price->setChannel($channel);

                    $prices[] = $price;
                }

                $value->setPrices($prices);

                $customerOption->addValue($value);
            }
        }

        $customerOption->setRequired($options['required']);

        foreach ($options['groups'] as $groupCode) {
            /** @var CustomerOptionGroupInterface $group */
            $group = $this->customerOptionGroupRepository->findOneBy(['code' => $groupCode]);

            if ($group !== null) {
                $groupAssoc = new CustomerOptionAssociation();

                $group->addOptionAssociation($groupAssoc);
                $customerOption->addGroupAssociation($groupAssoc);

                $this->em->persist($groupAssoc);
            }
        }

        $this->em->persist($customerOption);
    }

    public function generateRandom(int $amount)
    {
        $types = CustomerOptionTypeEnum::getConstList();

        $names = $this->getUniqueNames($amount);

        for ($i = 0; $i < $amount; ++$i) {
            $options = [];

            $options['code'] = $this->faker->uuid;
            $options['translations']['en_US'] = sprintf('CustomerOption "%s"', $names[$i]);
            $options['type'] = $this->faker->randomElement($types);
            $options['required'] = $this->faker->boolean;

            if (CustomerOptionTypeEnum::isSelect($options['type'])) {
                $values = [];
                $numValues = $this->faker->numberBetween(2, 4);
                $valueNames = $this->getUniqueNames($numValues);

                for ($j = 0; $j < $numValues; ++$j) {
                    $value = [];

                    $value['code'] = $this->faker->uuid;
                    $value['translations']['en_US'] = sprintf('Value "%s"', $valueNames[$j]);

                    $price = [];

                    $price['type'] = $this->faker->randomElement(['fixed', 'percent']);
                    $price['amount'] = $this->faker->numberBetween(100, 100000);
                    $price['percent'] = $this->faker->randomFloat(0, 1, 100);
                    $price['channel'] = $this->faker->randomElement($this->channelRepository->findAll());

                    $value['prices'][] = $price;
                    $values[] = $value;
                }

                $options['values'] = $values;
            }

            /** @var CustomerOptionGroupInterface[] $groups */
            $groups = $this->customerOptionGroupRepository->findAll();
            $groupCodes = [];

            foreach ($groups as $group) {
                $groupCodes[] = $group->getCode();
            }

            if (count($groupCodes) > 0) {
                $options['groups'] = $this->faker->randomElements($groupCodes);
            }

            try {
                $this->create($options);
            } catch (\Throwable $e) {
                dump($e->getMessage());
            }
        }
    }

    /**
     * @param int $amount
     *
     * @return array
     */
    private function getUniqueNames(int $amount): array
    {
        $names = [];

        $this->faker->unique($reset=true);
        for ($i = 0; $i < $amount; ++$i) {
            $names[] = $this->faker->unique()->word;
        }

        return $names;
    }

    private function getOptionsPrototype(): array
    {
        return [
            'code' => null,
            'translations' => [],
            'type' => null,
            'values' => [],
            'required' => false,
            'groups' => [],
        ];
    }
}
