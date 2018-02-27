<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Fixture;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
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
     * @var CustomerOptionValueRepositoryInterface
     */
    private $customerOptionValueRepository;

    /**
     * @var RepositoryInterface
     */
    private $customerOptionGroupRepository;

    /**
     * @var EntityRepository
     */
    private $channelRepository;

    public function __construct(
        EntityManagerInterface $em,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        EntityRepository $customerOptionGroupRepository,
        EntityRepository $channelRepository
    ) {
        $this->em = $em;
        $this->customerOptionValueRepository = $customerOptionValueRepository;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->channelRepository = $channelRepository;
    }

    public function load(array $options): void
    {
        if (!array_key_exists('customer_options', $options)) {
            return;
        }

        foreach ($options['customer_options'] as $optionConfig) {
            $customerOption = new CustomerOption();

            try {
                $customerOption->setCode($optionConfig['code']);

                foreach ($optionConfig['translations'] as $locale => $name) {
                    $customerOption->setCurrentLocale($locale);
                    $customerOption->setName($name);
                }
                $customerOption->setType($optionConfig['type']);

                foreach ($optionConfig['values'] as $valueConfig) {
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
                            $channel = $channels->first();
                        }

                        $price->setChannel($channel);

                        $this->em->persist($price);

                        $prices[] = $price;
                    }

                    $value->setPrices($prices);

                    $this->em->persist($value);

                    $customerOption->addValue($value);
                }

                $customerOption->setRequired(filter_var($optionConfig['required'], FILTER_VALIDATE_BOOLEAN));

                // Setup group associations
                foreach ($optionConfig['groups'] as $groupCode) {
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
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        $this->em->flush();
    }

    public function getName(): string
    {
        return 'brille24_customer_option';
    }

    public function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
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
