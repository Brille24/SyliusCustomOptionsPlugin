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

namespace Brille24\SyliusCustomerOptionsPlugin\Fixture;

use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class CustomerOptionFixture extends AbstractFixture implements FixtureInterface
{
    /** @var CustomerOptionFactory */
    private $factory;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(CustomerOptionFactory $factory, EntityManagerInterface $em)
    {
        $this->factory = $factory;
        $this->em      = $em;
    }

    public function load(array $options): void
    {
        // Getting the configured options
        $customConfiguration = array_key_exists('custom', $options) ? $options['custom'] : [];

        // When amount is given, generate config
        $autoConfiguration = array_key_exists('amount', $options)
            ? $this->factory->generateRandomConfiguration($options['amount']) : [];

        $customerOptions = [];
        foreach (array_merge($customConfiguration, $autoConfiguration) as $optionConfig) {
            try {
                $customerOptions[] = $this->factory->createFromConfig($optionConfig);
            } catch (\Throwable $e) {
                print_r($e->getMessage());
            }
        }

        foreach ($customerOptions as $option) {
            $this->em->persist($option);
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
                ->integerNode('amount')
                    ->min(0)
                ->end()
                ->arrayNode('custom')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()

                            ->scalarNode('code')
                                ->cannotBeEmpty()
                                ->isRequired()
                            ->end()

                            ->arrayNode('translations')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->scalarPrototype()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()

                            ->scalarNode('type')
                                ->defaultValue(CustomerOptionTypeEnum::TEXT)
                            ->end()

                            ->booleanNode('required')
                                ->defaultFalse()
                            ->end()

                            ->arrayNode('configuration')
                                ->scalarPrototype()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()

                            ->arrayNode('values')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('code')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->arrayNode('translations')
                                            ->isRequired()
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
                                                        ->cannotBeEmpty()
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
