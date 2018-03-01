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

namespace Brille24\CustomerOptionsPlugin\Fixture;

use Brille24\CustomerOptionsPlugin\Factory\CustomerOptionGroupFactory;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class CustomerOptionGroupFixture extends AbstractFixture
{
    private $factory;

    public function __construct(CustomerOptionGroupFactory $factory)
    {
        $this->factory = $factory;
    }

    public function load(array $options): void
    {
        if (array_key_exists('amount', $options)) {
            $this->factory->generateRandom($options['amount']);
        }

        foreach ($options['customer_option_groups'] as $groupConfig) {
            try {
                $this->factory->create($groupConfig);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }
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
