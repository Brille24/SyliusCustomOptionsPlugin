<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Context;

use Behat\Behat\Context\Context;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;

class TransformContext implements Context
{
    public function __construct(
        private CustomerOptionRepositoryInterface $customerOptionRepository,
        private CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository,
        private EntityManagerInterface $em,
    )
    {
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
}
