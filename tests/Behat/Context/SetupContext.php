<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context;


use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

class SetupContext implements Context
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
     * @Given I have a customer option :code named :name
     * @Given I have a customer option :code named :name in :locale
     */
    public function iHaveACustomerOptionNamed($code, $name, $locale = 'en_US')
    {
        $customerOption = new CustomerOption();
        $customerOption->setCode($code);
        $customerOption->setCurrentLocale($locale);
        $customerOption->setName($name);

        $this->customerOptionRepository->add($customerOption);
    }

    /**
     * @Given I have a customer option group :code named :name
     * @Given I have a customer option group :code named :name in :locale
     */
    public function iHaveACustomerOptionGroupNamed($code, $name, $locale = 'en_US')
    {
        $customerOptionGroup = new CustomerOptionGroup();
        $customerOptionGroup->setCode($code);
        $customerOptionGroup->setCurrentLocale($locale);
        $customerOptionGroup->setName($name);

        $this->customerOptionGroupRepository->add($customerOptionGroup);
    }

    /**
     * @Given customer option group :customerOptionGroup has option :customerOption
     */
    public function customerOptionGroupHasOption(
        CustomerOptionGroupInterface $customerOptionGroup,
        CustomerOptionInterface $customerOption
    )
    {
        $assoc = new CustomerOptionAssociation();

        $customerOptionGroup->addOptionAssociation($assoc);
        $customerOption->addGroupAssociation($assoc);
    }

    /**
     * @Given product :product has the customer option group :customerOptionGroup
     */
    public function productHasTheCustomerOptionGroup(
        ProductInterface $product,
        CustomerOptionGroupInterface $customerOptionGroup
    )
    {
        $product->setCustomerOptionGroup($customerOptionGroup);
    }
}