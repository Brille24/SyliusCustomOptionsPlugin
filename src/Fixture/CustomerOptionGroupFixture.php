<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Fixture;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class CustomerOptionGroupFixture extends AbstractFixture
{
    private $em;

    private $customerOptionRepository;

    private $productRepository;

    public function __construct(
        EntityManagerInterface $em,
        EntityRepository $customerOptionRepository,
        EntityRepository $productRepository
    ) {
        $this->em = $em;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->productRepository = $productRepository;
    }

    public function load(array $options): void
    {
        if (!array_key_exists('customer_option_groups', $options)) {
            return;
        }

        foreach ($options['customer_option_groups'] as $groupConfig) {
            $customerOptionGroup = new CustomerOptionGroup();

            try {
                $customerOptionGroup->setCode($groupConfig['code']);

                foreach ($groupConfig['translations'] as $locale => $name) {
                    $customerOptionGroup->setCurrentLocale($locale);
                    $customerOptionGroup->setName($name);
                }

                foreach ($groupConfig['options'] as $optionCode) {
                    /** @var CustomerOptionInterface $option */
                    $option = $this->customerOptionRepository->findOneBy(['code' => $optionCode]);

                    $optionAssoc = new CustomerOptionAssociation();

                    $option->addGroupAssociation($optionAssoc);
                    $customerOptionGroup->addOptionAssociation($optionAssoc);

                    $this->em->persist($optionAssoc);
                    $this->em->persist($option);
                }

                $products = $this->productRepository->findBy(['code' => $groupConfig['products']]);
                $customerOptionGroup->setProducts($products);

                $this->em->persist($customerOptionGroup);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
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
                ->arrayNode('customer_option_groups')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('code')
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('translations')
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
