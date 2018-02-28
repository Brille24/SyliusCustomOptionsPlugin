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
use Brille24\CustomerOptionsPlugin\Fixture\Factory\CustomerOptionFactory;
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
    private $factory;

    public function __construct(CustomerOptionFactory $factory) {
        $this->factory = $factory;
    }

    public function load(array $options): void
    {
        if(array_key_exists('amount', $options)){
            $this->factory->generateRandom($options['amount']);
        }

        foreach ($options['customer_options'] as $optionConfig) {
            try {
                $this->factory->create($optionConfig);
            } catch (\Throwable $e) {
                dump($e->getMessage());
            }
        }
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
