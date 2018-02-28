<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Fixture;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class CustomerOptionFixture extends AbstractFixture implements FixtureInterface
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
        EntityRepository $customerOptionGroupRepository,
        EntityRepository $channelRepository
    ) {
        $this->em = $em;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->channelRepository = $channelRepository;

        $this->faker = \Faker\Factory::create();
    }

    public function load(array $options): void
    {
        if(array_key_exists('amount', $options)){
            $this->generateRandom($options['amount']);
        }

        foreach ($options['customer_options'] as $optionConfig) {

            try {
                $customerOption = $this->create($optionConfig);

                $this->em->persist($customerOption);
            } catch (\Throwable $e) {
                dump($e->getMessage());
            }
        }



        $this->em->flush();
    }

    private function generateRandom(int $amount)
    {
        $types = CustomerOptionTypeEnum::getConstList();

        $names = $this->getUniqueNames($amount);

        for ($i = 0; $i < $amount; $i++){
            $options = [];

            $options['code'] = $this->faker->uuid;
            $options['translations']['en_US'] = sprintf('CustomerOption "%s"', $names[$i]);
            $options['type'] = $this->faker->randomElement($types);
            $options['required'] = $this->faker->boolean;

            if(CustomerOptionTypeEnum::isSelect($options['type'])) {
                $values = [];
                $numValues = $this->faker->numberBetween(2, 4);
                $valueNames = $this->getUniqueNames($numValues);

                for ($j = 0; $j < $numValues; $j++){
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

            foreach ($groups as $group){
                $groupCodes[] = $group->getCode();
            }

            if(count($groupCodes) > 0) {
                $options['groups'] = $this->faker->randomElements($groupCodes);
            }

            try {
                $customerOption = $this->create($options);
                $this->em->persist($customerOption);
            } catch (\Throwable $e) {
                dump($e->getMessage());
            }
        }
    }

    /**
     * @param array $options
     * @return CustomerOptionInterface
     * @throws \Exception
     */
    private function create(array $options): CustomerOptionInterface
    {
        $options = array_merge($this->getOptionsPrototype(), $options);

        $customerOption = new CustomerOption();

        $customerOption->setCode($options['code']);

        foreach ($options['translations'] as $locale => $name) {
            $customerOption->setCurrentLocale($locale);
            $customerOption->setName($name);
        }
        $customerOption->setType($options['type']);

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

                if($channel === null){
                    $channels = new ArrayCollection($this->channelRepository->findAll());
                    $channel = $channels->first() ? $channels->first() : null;
                }

                $price->setChannel($channel);

                $prices[] = $price;
            }

            $value->setPrices($prices);

            $customerOption->addValue($value);
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

        return $customerOption;
    }

    /**
     * @param int $amount
     *
     * @return array
     */
    private function getUniqueNames(int $amount): array
    {
        $names = [];

        for ($i = 0; $i < $amount; ++$i) {
            $name = $this->faker->word;
            while (in_array($name, $names)) {
                $name = $this->faker->word;
            }
            $names[] = $name;
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

    public function getName(): string
    {
        return 'brille24_customer_option';
    }

    public function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('amount')
                    ->min(0)
                ->end()
                ->arrayNode('customer_options')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()

                            ->scalarNode('code')
                                ->cannotBeEmpty()
                            ->end()

                            ->arrayNode('translations')
                                ->requiresAtLeastOneElement()
                                ->scalarPrototype()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()

                            ->scalarNode('type')
                                ->cannotBeEmpty()
                            ->end()

                            ->booleanNode('required')
                            ->end()

                            ->arrayNode('values')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('code')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->arrayNode('translations')
                                            ->requiresAtLeastOneElement()
                                            ->scalarPrototype()
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                        ->arrayNode('prices')
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('type')
                                                        ->defaultValue('fixed')
                                                    ->end()
                                                    ->integerNode('amount')
                                                        ->defaultValue(0)
                                                    ->end()
                                                    ->floatNode('percent')
                                                        ->defaultValue(0)
                                                    ->end()
                                                    ->scalarNode('channel')
                                                        ->defaultValue('default')
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('groups')
                                ->prototype('scalar')->end()
                            ->end()

                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
