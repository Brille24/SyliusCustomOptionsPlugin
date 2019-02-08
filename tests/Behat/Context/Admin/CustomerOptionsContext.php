<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Context\Admin;

use Behat\Behat\Context\Context;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption\CreatePage;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption\UpdatePage;
use Webmozart\Assert\Assert;

class CustomerOptionsContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var CreatePage */
    private $createPage;

    /** @var UpdatePage */
    private $updatePage;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to create a new customer option
     */
    public function iWantToCreateANewCustomerOption()
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to browse customer options
     */
    public function iWantToBrowseCustomerOptions()
    {
        $this->indexPage->open();
    }

    /**
     * @Given I want to edit customer option :customerOption
     */
    public function iWantToModifyCustomerOption(CustomerOptionInterface $customerOption)
    {
        $this->updatePage->open(['id' => $customerOption->getId()]);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setCode($code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setName($name);
    }

    /**
     * @When I specify its type as :type
     */
    public function iSpecifyItsTypeAs($type)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseType($type);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then the customer option :customerOptionName should appear in the registry
     */
    public function theCustomerOptionShouldAppearInTheRegistry($customerOptionName)
    {
        $this->iWantToBrowseCustomerOptions();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $customerOptionName]));
    }

    /**
     * @When I set it required
     */
    public function iSetItRequired()
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setRequired();
    }

    /**
     * @Then I should see configuration for :config
     */
    public function iShouldSeeConfigurationFor($config)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true($currentPage->hasConfiguration($config));
    }

    /**
     * @Then :customerOption should have configuration :config
     */
    public function shouldHaveConfiguration(CustomerOptionInterface $customerOption, $config)
    {
        $this->iWantToModifyCustomerOption($customerOption);

        Assert::true($this->updatePage->hasConfiguration($config));
    }

    /**
     * @When I set :field to :value
     */
    public function iSetTo($field, $value)
    {
        $this->createPage->setField($field, $value);
    }

    /**
     * @Then I should see a link :name
     */
    public function iShouldSeeALink($name)
    {
        Assert::true($this->createPage->hasLink($name));
    }

    /**
     * @When I add a value :code with name :name
     */
    public function iAddAValueWithName($code, $name)
    {
        $this->createPage->addValue($code, $name);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should see price configuration for value :valueName in channel :channelName
     * @Then I should see price configuration for value :valueName
     */
    public function iShouldSeePriceConfigurationForValue(string $valueName, string $channelName = 'WEB-US')
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true($currentPage->hasPriceConfigurationForValue($valueName, $channelName));
    }

    /**
     * @When I delete customer option :name
     */
    public function iDeleteCustomerOption($name)
    {
        $this->indexPage->deleteResourceOnPage(['name' => $name]);
    }

    /**
     * @Then the customer option :name should not appear in the registry
     */
    public function theCustomerOptionShouldNotAppearInTheRegistry($name)
    {
        $this->iWantToBrowseCustomerOptions();
        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then I should be notified that code is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessage('code'),
            'brille24.customer_option.not_null'
        );
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same(
            $currentPage->getValidationMessage('code'),
            'brille24.customer_options.unique'
        );
    }
}
