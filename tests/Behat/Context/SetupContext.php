<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context;


use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

class SetupContext implements Context
{
    /** @var EntityManagerInterface  */
    private $em;

    /** @var CustomerOptionRepositoryInterface  */
    private $customerOptionRepository;

    /** @var CustomerOptionGroupRepositoryInterface  */
    private $customerOptionGroupRepository;

    public function __construct(
        EntityManagerInterface $em,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionGroupRepositoryInterface $customerOptionGroupRepository
    )
    {
        $this->em = $em;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
    }

    /**
     * @Given I have a customer option :code named :name
     * @Given I have a customer option :code named :name in :locale
     * @Given I have a customer option :code named :name with type :type
     * @Given I have a customer option :code named :name in :locale with type :type
     */
    public function iHaveACustomerOptionNamed(string $code, string $name, string $locale = 'en_US', string $type)
    {
        $customerOption = new CustomerOption();
        $customerOption->setCode($code);
        $customerOption->setCurrentLocale($locale);
        $customerOption->setName($name);
        $customerOption->setType($type);

        $this->customerOptionRepository->add($customerOption);
    }


    /**
     * @Given customer option :customerOption has a value :valueName
     * @Given customer option :customerOption has a value named :valueName in :locale
     */
    public function customerOptionHasAValue(CustomerOptionInterface $customerOption, string $valueName, string $locale = 'en_US')
    {
        $value = new CustomerOptionValue();
        $value->setCode(strtolower(str_replace(' ', '_', $valueName)));
        $value->setCurrentLocale($locale);
        $value->setName($valueName);

        $customerOption->addValue($value);
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
        $customerOptionGroup->addProduct($product);

        $this->em->persist($product);
        $this->em->persist($customerOptionGroup);

        $this->em->flush();
    }

    /**
     * @Given customer option :customerOption is required
     */
    public function customerOptionIsRequired(CustomerOptionInterface $customerOption)
    {
        $customerOption->setRequired(true);

        $this->em->persist($customerOption);
        $this->em->flush();
    }

    /**
     * @Given customer option :customerOption is not required
     */
    public function customerOptionIsNotRequired(CustomerOptionInterface $customerOption)
    {
        $customerOption->setRequired(false);

        $this->em->persist($customerOption);
        $this->em->flush();
    }

}