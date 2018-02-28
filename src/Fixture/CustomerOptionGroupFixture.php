<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Fixture;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class CustomerOptionGroupFixture extends AbstractFixture
{
    private $em;

    private $customerOptionRepository;

    private $productRepository;

    private $faker;

    public function __construct(
        EntityManagerInterface $em,
        EntityRepository $customerOptionRepository,
        EntityRepository $productRepository
    ) {
        $this->em = $em;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->productRepository = $productRepository;

        $this->faker = \Faker\Factory::create();
    }

    public function load(array $options): void
    {
        if (array_key_exists('amount', $options)) {
            $this->generateRandom($options['amount']);
        }

        foreach ($options['customer_option_groups'] as $groupConfig) {
            try {
                $customerOptionGroup = $this->create($groupConfig);

                $this->em->persist($customerOptionGroup);
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }

        $this->em->flush();
    }

    private function generateRandom(int $amount): void
    {
        $customerOptionsCodes = [];

        /** @var CustomerOptionInterface $customerOption */
        foreach ($this->customerOptionRepository->findAll() as $customerOption){
            $customerOptionsCodes[] = $customerOption->getCode();
        }

        $productCodes = [];

        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product){
            $productCodes[] = $product->getCode();
        }

        $names = $this->getUniqueNames($amount);

        for($i = 0; $i < $amount; $i++) {
            $options = [];

            $options['code'] = $this->faker->uuid;
            $options['translations']['en_US'] = sprintf('CustomerOptionGroup "%s"', $names[$i]);

            if (count($customerOptionsCodes) > 0) {
                $options['options'] = $this->faker->randomElements($customerOptionsCodes);
            }

            if (count($productCodes) > 0) {
                $options['products'] = $this->faker->randomElements($productCodes);
            }

            try{
                $customerOptionGroup = $this->create($options);
                $this->em->persist($customerOptionGroup);
            }catch (\Throwable $e){
                dump($e->getMessage());
            }
        }
    }

    private function create(array $options): CustomerOptionGroupInterface
    {
        $options = array_merge($this->getOptionsPrototype(), $options);

        $customerOptionGroup = new CustomerOptionGroup();

        $customerOptionGroup->setCode($options['code']);

        foreach ($options['translations'] as $locale => $name) {
            $customerOptionGroup->setCurrentLocale($locale);
            $customerOptionGroup->setName($name);
        }

        foreach ($options['options'] as $optionCode) {
            /** @var CustomerOptionInterface $option */
            $option = $this->customerOptionRepository->findOneBy(['code' => $optionCode]);

            $optionAssoc = new CustomerOptionAssociation();

            $option->addGroupAssociation($optionAssoc);
            $customerOptionGroup->addOptionAssociation($optionAssoc);

            $this->em->persist($optionAssoc);
            $this->em->persist($option);
        }

        $products = $this->productRepository->findBy(['code' => $options['products']]);
        $customerOptionGroup->setProducts($products);

        return $customerOptionGroup;
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

    private function getOptionsPrototype(){
        return [
            'code' => null,
            'translations' => [],
            'options' => [],
            'products' => [],
        ];
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
