<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Context\Admin;

use Behat\Behat\Context\Context;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Product\CreateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product\CreateConfigurableProductPage;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product\CreateSimpleProductPage;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product\UpdateConfigurableProductPage;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product\UpdateSimpleProductPage;
use Webmozart\Assert\Assert;

class ProductsContext implements Context
{
    /** @var UpdateSimpleProductPageInterface */
    private $updatePageSimple;

    /** @var UpdateConfigurableProductPageInterface */
    private $updatePageConfigurable;

    /** @var CreateSimpleProductPageInterface */
    private $createPageSimple;

    /** @var CreateConfigurableProductPageInterface */
    private $createPageConfigurable;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var EntityRepository */
    private $customerOptionValuePriceRepository;

    public function __construct(
        UpdateSimpleProductPageInterface $updateSimpleProductPage,
        UpdateConfigurableProductPageInterface $updateConfigurableProductPage,
        CreateSimpleProductPageInterface $createPageSimple,
        CreateConfigurableProductPageInterface $createPageConfigurable,
        CurrentPageResolverInterface $currentPageResolver,
        EntityRepository $customerOptionValuePriceRepository
    ) {
        $this->updatePageSimple = $updateSimpleProductPage;
        $this->updatePageConfigurable = $updateConfigurableProductPage;
        $this->createPageSimple = $createPageSimple;
        $this->createPageConfigurable = $createPageConfigurable;
        $this->currentPageResolver = $currentPageResolver;
        $this->customerOptionValuePriceRepository = $customerOptionValuePriceRepository;
    }

    /**
     * @Given I want to create a new simple product
     */
    public function iWantToCreateANewSimpleProduct()
    {
        $this->createPageSimple->open();
    }

    /**
     * @Given I want to create a new configurable product
     */
    public function iWantToCreateANewConfigurableProduct()
    {
        $this->createPageConfigurable->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->specifyCode($code);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        /** @var CreatePageInterface $currentPage */
        $currentPage = $this->resolveCurrentPage();

        $currentPage->create();
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     */
    public function iRenameItToIn($name, $language)
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->nameItIn($name, $language);
    }

    /**
     * @Given I want to modify the :product product
     */
    public function iWantToModifyTheProduct(ProductInterface $product)
    {
        if ($product->isSimple()) {
            $this->updatePageSimple->open(['id' => $product->getId()]);
        } else {
            $this->updatePageConfigurable->open(['id' => $product->getId()]);
        }
    }

    /**
     * @When I open the customer options tab
     */
    public function iOpenTheCustomerOptionsTab()
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->openCustomerOptionsTab();
    }

    /**
     * @When I choose customer option group :customerOptionGroupName
     */
    public function iChooseCustomerOptionGroup(string $customerOptionGroupName)
    {
        $this->iOpenTheCustomerOptionsTab();

        $currentPage = $this->resolveCurrentPage();

        $currentPage->selectCustomerOptionGroup($customerOptionGroupName);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->saveChanges();
    }

    /**
     * @return CreateConfigurableProductPage|CreateSimpleProductPage|UpdateConfigurableProductPage|UpdateSimpleProductPage|SymfonyPageInterface
     */
    private function resolveCurrentPage()
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->updatePageSimple,
            $this->updatePageConfigurable,
            $this->createPageSimple,
            $this->createPageConfigurable,
        ]);
    }

    /**
     * @Then product :product should have customer option group :customerOptionGroup
     */
    public function productShouldHaveCustomerOptionGroup(ProductInterface $product, CustomerOptionGroupInterface $customerOptionGroup)
    {
        Assert::same($product->getCustomerOptionGroup(), $customerOptionGroup);
    }

    /**
     * @When I add a new customer option value price
     */
    public function iAddANewCustomerOptionValuePrice()
    {
        $this->iOpenTheCustomerOptionsTab();

        $currentPage = $this->resolveCurrentPage();

        $currentPage->addCustomerOptionValuePrice();
    }

    /**
     * @When I select customer option value :valueName
     */
    public function iSelectCustomerOptionValue(string $valueName)
    {
        $this->iOpenTheCustomerOptionsTab();

        $currentPage = $this->resolveCurrentPage();

        $currentPage->chooseOptionValue($valueName);
    }

    /**
     * @When I set amount to :amount
     */
    public function iSetAmountTo(int $amount)
    {
        $this->iOpenTheCustomerOptionsTab();

        $currentPage = $this->resolveCurrentPage();

        $currentPage->setValuePriceAmount($amount);
    }

    /**
     * @When I set type to :type
     */
    public function iSetTypeTo(string $type)
    {
        $this->iOpenTheCustomerOptionsTab();

        $currentPage = $this->resolveCurrentPage();

        $currentPage->setValuePriceType($type);
    }

    /**
     * @Then product :product should have a customer option value price with amount :amount
     */
    public function productShouldHaveACustomerOptionValuePriceWithAmount(ProductInterface $product, int $amount)
    {
        $valuePrices = $this->customerOptionValuePriceRepository->findBy(['product' => $product]);

        $result = false;

        /** @var CustomerOptionValuePriceInterface $valuePrice */
        foreach ($valuePrices as $valuePrice) {
            if ($valuePrice->getAmount() === $amount * 100) {
                $result = true;
            }
        }

        Assert::true($result);
    }
}
