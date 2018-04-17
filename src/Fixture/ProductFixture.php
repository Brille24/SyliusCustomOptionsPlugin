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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ProductFixture as BaseProductFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ProductFixture extends BaseProductFixture
{
    public function __construct(ObjectManager $objectManager, ExampleFactoryInterface $exampleFactory)
    {
        parent::__construct($objectManager, $exampleFactory);
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        parent::configureResourceNode($resourceNode);

        $resourceNode
            ->children()
                ->scalarNode('customer_option_group')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('customer_option_value_prices')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('value_code')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
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
        ;
    }
}
