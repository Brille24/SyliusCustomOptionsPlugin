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

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\{
    CustomerOptionAssociation, CustomerOptionGroup, CustomerOptionGroupInterface, CustomerOptionInterface
};
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Throwable;

class CustomerOptionGroupFactory
{
    /** @var EntityManagerInterface  */
    private $em;

    /** @var CustomerOptionRepositoryInterface  */
    private $customerOptionRepository;

    /** @var ProductRepositoryInterface  */
    private $productRepository;

    /** @var \Faker\Generator */
    private $faker;

    public function __construct(
        EntityManagerInterface $em,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->em = $em;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->productRepository = $productRepository;

        $this->faker = Factory::create();
    }

    private function validateOptions(array $options){
        if(count($options['translations']) === 0){
            return false;
        }

        return true;
    }

    public function generateRandom(int $amount): array
    {
        $customerOptionsCodes = [];

        /** @var CustomerOptionInterface $customerOption */
        foreach ($this->customerOptionRepository->findAll() as $customerOption) {
            $customerOptionsCodes[] = $customerOption->getCode();
        }

        $productCodes = [];

        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product) {
            $productCodes[] = $product->getCode();
        }

        $names = $this->getUniqueNames($amount);

        $customerOptionGroups = [];

        for ($i = 0; $i < $amount; ++$i) {
            $options = [];

            $options['code'] = $this->faker->uuid;
            $options['translations']['en_US'] = sprintf('CustomerOptionGroup "%s"', $names[$i]);

            if (count($customerOptionsCodes) > 0) {
                $options['options'] = $this->faker->randomElements($customerOptionsCodes);
            }

            if (count($productCodes) > 0) {
                $options['products'] = $this->faker->randomElements($productCodes);
            }

            try {
                $customerOptionGroups[] = $this->create($options);
            } catch (Throwable $e) {
                dump($e->getMessage());
            }
        }

        return $customerOptionGroups;
    }

    /**
     * @param array $options
     * @return CustomerOptionGroupInterface
     * @throws \Exception
     */
    public function create(array $options): CustomerOptionGroupInterface
    {
        $options = array_merge($this->getOptionsPrototype(), $options);

        if($this->validateOptions($options) === false){
            throw new \Exception('Invalid options,');
        }

        $customerOptionGroup = new CustomerOptionGroup();

        $customerOptionGroup->setCode($options['code']);

        foreach ($options['translations'] as $locale => $name) {
            $customerOptionGroup->setCurrentLocale($locale);
            $customerOptionGroup->setName($name);
        }

        foreach ($options['options'] as $optionCode) {
            /** @var CustomerOptionInterface $option */
            $option = $this->customerOptionRepository->findOneByCode($optionCode);

            if ($option !== null) {
                $optionAssoc = new CustomerOptionAssociation();

                $option->addGroupAssociation($optionAssoc);
                $customerOptionGroup->addOptionAssociation($optionAssoc);

                $this->em->persist($optionAssoc);
                $this->em->persist($option);
            }
        }


        $products = $this->productRepository->findBy(['code' => $options['products']]);
        $customerOptionGroup->setProducts($products);

        $this->em->persist($customerOptionGroup);

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
            $names[] = $this->faker->unique()->word;
        }

        return $names;
    }

    private function getOptionsPrototype()
    {
        return [
            'code' => null,
            'translations' => [],
            'options' => [],
            'products' => [],
        ];
    }
}
