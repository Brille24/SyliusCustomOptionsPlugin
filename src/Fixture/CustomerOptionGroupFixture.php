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

use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionGroupFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class CustomerOptionGroupFixture extends AbstractFixture
{
    /** @var CustomerOptionGroupFactoryInterface */
    private $factory;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(CustomerOptionGroupFactoryInterface $factory, EntityManagerInterface $em)
    {
        $this->factory = $factory;
        $this->em      = $em;
    }

    public function load(array $options): void
    {
        $customerOptionsGroups = [];

        if (array_key_exists('amount', $options)) {
            $customerOptionsGroups = $this->factory->generateRandom($options['amount']);
        }

        foreach ($options['custom'] as $groupConfig) {
            try {
                $customerOptionsGroups[] = $this->factory->createFromConfig($groupConfig);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        foreach ($customerOptionsGroups as $group) {
            $this->em->persist($group);
        }

        $this->em->flush();
    }

    public function getName(): string
    {
        return 'brille24_customer_option_group';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
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
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('translations')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                            ->arrayNode('validators')
                                ->requiresAtLeastOneElement()
                                ->arrayPrototype()
                                    ->children()
                                        ->arrayNode('conditions')
                                            ->requiresAtLeastOneElement()
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('customer_option')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                    ->scalarNode('comparator')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                    ->scalarNode('value')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('constraints')
                                            ->requiresAtLeastOneElement()
                                            ->arrayPrototype()
                                                ->children()
                                                    ->scalarNode('customer_option')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                    ->scalarNode('comparator')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                    ->scalarNode('value')
                                                        ->isRequired()
                                                        ->cannotBeEmpty()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('error_messages')
                                            ->requiresAtLeastOneElement()
                                            ->scalarPrototype()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('products')
                                ->scalarPrototype()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
