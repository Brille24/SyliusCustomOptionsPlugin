<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\DependencyInjection;

use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionGroupTranslation;
use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionGroupTranslationInterface;
use Brille24\CustomerOptionsBundle\Entity\Product;
use Brille24\CustomerOptionsBundle\Entity\ProductInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('brille24_customer_options');

        // Adding new resources
        $this->addResourceSection($rootNode);

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customer_option_group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CustomerOptionGroup::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CustomerOptionGroupInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(CustomerOptionGroupTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(CustomerOptionGroupTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
