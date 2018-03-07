<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context\Admin;


use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOptionGroup\CreatePage;
use Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOptionGroup\UpdatePage;
use Webmozart\Assert\Assert;

class CustomerOptionGroupsContext implements Context
{
    private $createPage;

    private $updatePage;

    private $indexPage;

    private $currentPageResolver;

    public function __construct(
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        IndexPageInterface $indexPage,
        CurrentPageResolverInterface $currentPageResolver
    )
    {
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->indexPage = $indexPage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to create a new customer option group
     */
    public function iWantToCreateANewCustomerOptionGroup()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->fillField('Code', $code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->fillField('Name', $name);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then the customer option group :name should appear in the registry
     */
    public function theCustomerOptionGroupShouldAppearInTheRegistry($name)
    {
        $this->iWantToBrowseCustomerOptionGroups();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Given I want to browse customer option groups
     */
    public function iWantToBrowseCustomerOptionGroups()
    {
        $this->indexPage->open();
    }

    /**
     * @When I delete customer option group :name
     */
    public function iDeleteCustomerOptionGroup($name)
    {
        $this->iWantToBrowseCustomerOptionGroups();

        $this->indexPage->deleteResourceOnPage(['name' => $name]);
    }

    /**
     * @Then the customer option group :name should not appear in the registry
     */
    public function theCustomerOptionGroupShouldNotAppearInTheRegistry($name)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Given I want to edit customer option group :customerOptionGroup
     */
    public function iWantToEditCustomerOptionGroup(CustomerOptionGroupInterface $customerOptionGroup)
    {
        $this->updatePage->open(['id' => $customerOptionGroup->getId()]);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I add a customer option :customerOptionName
     * @When I add a customer option :customerOptionName with position :position
     */
    public function iAddACustomerOption(string $customerOptionName, int $position = 0)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addOption();
        $currentPage->chooseOption($customerOptionName, $position);
    }

    /**
     * @Then the customer option group :customerOptionGroup should have option :customerOption
     */
    public function theCustomerOptionGroupShouldHaveOption(
        CustomerOptionGroupInterface $customerOptionGroup,
        CustomerOptionInterface $customerOption
    )
    {
        $result = false;

        foreach ($customerOptionGroup->getOptions() as $option){
            if($option === $customerOption){
                $result = true;
            }
        }

        Assert::true($result);
    }
}
