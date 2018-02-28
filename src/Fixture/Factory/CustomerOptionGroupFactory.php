<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Fixture\Factory;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class CustomerOptionGroupFactory
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

    public function generateRandom(int $amount): void
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
                $this->create($options);
            }catch (\Throwable $e){
                dump($e->getMessage());
            }
        }
    }

    public function create(array $options)
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

        $this->em->persist($customerOptionGroup);
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
}