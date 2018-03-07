<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context\Admin;


use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
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

    public function __construct(
        UpdateSimpleProductPageInterface $updateSimpleProductPage,
        UpdateConfigurableProductPageInterface $updateConfigurableProductPage,
        CurrentPageResolverInterface $currentPageResolver
    )
    {
        $this->updatePageSimple = $updateSimpleProductPage;
        $this->updatePageConfigurable = $updateConfigurableProductPage;
        $this->currentPageResolver = $currentPageResolver;
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
     * @When I choose customer option group :customerOptionGroupName
     */
    public function iChooseCustomerOptionGroup(string $customerOptionGroupName)
    {
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

}