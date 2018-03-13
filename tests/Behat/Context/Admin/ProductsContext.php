<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context\Admin;


use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Brille24\CustomerOptionsPlugin\Behat\Page\Product\UpdateConfigurableProductPage;
use Tests\Brille24\CustomerOptionsPlugin\Behat\Page\Product\UpdateSimpleProductPage;
use Webmozart\Assert\Assert;

class ProductsContext implements Context
{
    /** @var UpdateSimpleProductPageInterface  */
    private $updatePageSimple;

    /** @var UpdateConfigurableProductPageInterface  */
    private $updatePageConfigurable;

    /** @var CurrentPageResolverInterface  */
    private $currentPageResolver;

    /** @var EntityRepository  */
    private $customerOptionValuePriceRepository;

    public function __construct(
        UpdateSimpleProductPageInterface $updateSimpleProductPage,
        UpdateConfigurableProductPageInterface $updateConfigurableProductPage,
        CurrentPageResolverInterface $currentPageResolver,
        EntityRepository $customerOptionValuePriceRepository
    )
    {
        $this->updatePageSimple = $updateSimpleProductPage;
        $this->updatePageConfigurable = $updateConfigurableProductPage;
        $this->currentPageResolver = $currentPageResolver;
        $this->customerOptionValuePriceRepository = $customerOptionValuePriceRepository;
    }

    /**
     * @Given I want to modify the :product product
     */
    public function iWantToModifyTheProduct(ProductInterface $product)
    {
        if($product->isSimple()){
            $this->updatePageSimple->open(['id' => $product->getId()]);
        }else{
            $this->updatePageConfigurable->open(['id' => $product->getId()]);
        }
    }

    /**
     * @When I open the customer options tab
     */
    public function iOpenTheCustomerOptionsTab(){
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
     * @return UpdateConfigurableProductPage|UpdateSimpleProductPage|SymfonyPageInterface
     */
    private function resolveCurrentPage()
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->updatePageSimple,
            $this->updatePageConfigurable,
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
        foreach ($valuePrices as $valuePrice){
            if($valuePrice->getAmount() === $amount * 100){
                $result = true;
            }
        }

        Assert::true($result);
    }
}