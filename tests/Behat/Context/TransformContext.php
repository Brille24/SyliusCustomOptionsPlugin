<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

class TransformContext implements Context
{
    /** @var CustomerOptionRepositoryInterface */
    private $customerOptionRepository;

    /** @var CustomerOptionGroupRepositoryInterface */
    private $customerOptionGroupRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $em
    ) {
        $this->customerOptionRepository = $customerOptionRepository;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->productRepository = $productRepository;
        $this->em = $em;
    }

    /**
     * @Transform :customerOption
     * @Transform :customerOption in locale :locale
     */
    public function getCustomerOptionByName(string $name, string $locale = 'en_US'): CustomerOptionInterface
    {
        $customerOption = $this->customerOptionRepository->findByName($name, $locale);

        Assert::true(count($customerOption) > 0);

        return $customerOption[0];
    }

    /**
     * @Transform :customerOptionGroup
     * @Transform :customerOptionGroup in locale :locale
     */
    public function getCustomerOptionGroupByName(string $name, string $locale = 'en_US'): CustomerOptionGroupInterface
    {
        $customerOptionGroups = $this->customerOptionGroupRepository->findByName($name, $locale);

        Assert::true(count($customerOptionGroups) > 0);

        $this->em->refresh($customerOptionGroups[0]);

        return $customerOptionGroups[0];
    }

    /**
     * @Transform /^product(?:|s) "([^"]+)"$/
     * @Transform /^"([^"]+)" product(?:|s)$/
     * @Transform /^(?:a|an) "([^"]+)"$/
     * @Transform :product
     */
    public function getProductByName($productName)
    {
        $products = $this->productRepository->findByName($productName, 'en_US');

        Assert::eq(
            count($products),
            1,
            sprintf('%d products has been found with name "%s".', count($products), $productName)
        );

        $this->em->refresh($products[0]);

        return $products[0];
    }

    /**
     * @Transform /^products "([^"]+)" and "([^"]+)"$/
     * @Transform /^products "([^"]+)", "([^"]+)" and "([^"]+)"$/
     */
    public function getProductsByNames(...$productsNames)
    {
        return array_map(function ($productName) {
            return $this->getProductByName($productName);
        }, $productsNames);
    }
}
