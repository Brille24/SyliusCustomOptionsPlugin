<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\DependencyInjection;

use Brille24\CustomerOptionsBundle\Entity\CustomerOptionGroup;
use Brille24\CustomerOptionsBundle\Entity\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsBundle\Entity\CustomerOptionGroupTranslation;
use Brille24\CustomerOptionsBundle\Entity\CustomerOptionGroupTranslationInterface;
use Brille24\CustomerOptionsBundle\Entity\Product;
use Brille24\CustomerOptionsBundle\Entity\ProductInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
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
        $rootNode    = $treeBuilder->root('brille24_customer_options_bundle');
        $rootNode->addDefaultChildrenIfNoneSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end();

        $this->addResourceSection($rootNode);
        $this->addOverrides($treeBuilder);

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customOptionGroup')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CustomerOptionGroup::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CustomerOptionGroupInterface::class)->cannotBeEmpty()->end()
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

    private function addOverrides(TreeBuilder $treeBuilder) {
        // Overriding the sylius product
        $treeBuilder->root('sylius_product')
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->variableNode('options')->end()
                            ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('model')->defaultValue(Product::class)->cannotBeEmpty()->end()
                                    ->scalarNode('model')->defaultValue(ProductInterface::class)->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                ->end()
            ->end();
    }
}
