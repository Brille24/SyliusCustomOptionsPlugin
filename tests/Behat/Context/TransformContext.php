<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context;


use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Webmozart\Assert\Assert;

class TransformContext implements Context
{
    /** @var CustomerOptionRepositoryInterface  */
    private $customerOptionRepository;

    /** @var CustomerOptionGroupRepositoryInterface  */
    private $customerOptionGroupRepository;

    public function __construct(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository
    )
    {
        $this->customerOptionRepository = $customerOptionRepository;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
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

        return $customerOptionGroups[0];
    }
}