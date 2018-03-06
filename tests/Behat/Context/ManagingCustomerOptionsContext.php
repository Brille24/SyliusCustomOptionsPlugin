<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context;


use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOption\CreatePage;
use Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOption\UpdatePage;
use Webmozart\Assert\Assert;

class ManagingCustomerOptionsContext implements Context
{
    /** @var IndexPageInterface  */
    private $indexPage;

    /** @var CreatePage  */
    private $createPage;

    /** @var UpdatePage  */
    private $updatePage;

    /** @var CustomerOptionRepositoryInterface */
    private $customerOptionRepository;

    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CustomerOptionRepositoryInterface $customerOptionRepository
    )
    {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->customerOptionRepository = $customerOptionRepository;
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
        $this->createPage->setCode($code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        $this->createPage->setName($name);
    }

    /**
     * @When I specify its type as :type
     */
    public function iSpecifyItsTypeAs($type)
    {
        $this->createPage->chooseType($type);
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
        $this->createPage->setRequired();
    }

    /**
     * @Then I should see configuration for :config
     */
    public function iShouldSeeConfigurationFor($config)
    {
        Assert::true($this->createPage->hasConfiguration($config));
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
     * @Transform :customerOption
     */
    public function getCustomerOptionByName(string $name): CustomerOptionInterface
    {
        $customerOption = $this->customerOptionRepository->findByName($name, 'en_US');

        Assert::true(count($customerOption) > 0);

        return $customerOption[0];
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
     * @Then I should see price configuration for value :arg1
     */
    public function iShouldSeePriceConfigurationForValue($arg1)
    {
        throw new PendingException();
    }
}
