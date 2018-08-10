<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Context\Admin;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConditionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use InvalidArgumentException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOptionGroup\UpdatePage;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOptionGroup\CreatePage;
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
    ) {
        $this->createPage          = $createPage;
        $this->updatePage          = $updatePage;
        $this->indexPage           = $indexPage;
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
    ) {
        $result = false;

        foreach ($customerOptionGroup->getOptions() as $option) {
            if ($option === $customerOption) {
                $result = true;
            }
        }

        Assert::true($result);
    }

    /**
     * @When I add a validator
     */
    public function iAddAValidator()
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addValidator();
    }

    /**
     * @When I add a condition
     */
    public function iAddACondition()
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addCondition();
    }

    /**
     * @When I add a constraint
     */
    public function iAddAConstraint()
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->addConstraint();
    }

    /**
     * @When I pick :customerOption as the conditions customer option
     */
    public function iPickAsTheConditionsCustomerOption(CustomerOptionInterface $customerOption)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseOptionForCondition($customerOption->getName());

        $this->iSaveMyChanges();
    }

    /**
     * @Then the conditions value with customer option type :optionType should be of type :type
     */
    public function theConditionsValueShouldBeOfType(string $optionType, string $type)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getConditionValueType($optionType), $type);
    }

    /**
     * @Then I should see :num validators
     */
    public function iShouldSeeValidators(int $num)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->countValidators(), $num);
    }

    /**
     * @When I pick :customerOption as the constraints customer option
     */
    public function iPickAsTheConstraintsCustomerOption(CustomerOptionInterface $customerOption)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseOptionForConstraint($customerOption->getName());

        $this->iSaveMyChanges();
    }

    /**
     * @Then the constraints value with customer option type :optionType should be of type :type
     */
    public function theConstraintsValueWithCustomerOptionTypeShouldBeOfType($optionType, $type)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getConstraintValueType($optionType), $type);
    }

    /**
     * @When I delete a validator
     */
    public function iDeleteAValidator()
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->deleteValidator();
    }

    /**
     * @Then the conditions available comparators should be :comparators
     */
    public function theConditionsAvailableComparatorsShouldBe($comparators)
    {
        $comparators = explode(',', $string = preg_replace('/\s+/', '', $comparators));

        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $available = $currentPage->getAvailableConditionComparators();

        $diff = array_diff($comparators, $available);

        Assert::count($diff, 0);
    }

    /**
     * @Then the constraints available comparators should be :comparators
     */
    public function theConstraintsAvailableComparatorsShouldBe($comparators)
    {
        $comparators = explode(',', $string = preg_replace('/\s+/', '', $comparators));

        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $available = $currentPage->getAvailableConstraintComparators();

        $diff = array_diff($comparators, $available);

        Assert::count($diff, 0);
    }

    /**
     * @When I select :comparator as the conditions comparator
     */
    public function iSelectAsTheConditionsComparator($comparator)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseComparatorForCondition($comparator);
    }

    /**
     * @When I enter :value as value for the condition with customer option :customerOption
     */
    public function iEnterAsTheConditionsValue($value, CustomerOptionInterface $customerOption)
    {
        $value = $this->prepareValue($value, $customerOption->getType());

        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setConditionValue($value, $customerOption->getType());
    }

    /**
     * @When I select :comparator as the constraints comparator
     */
    public function iSelectAsTheConstraintsComparator($comparator)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseComparatorForConstraint($comparator);
    }

    /**
     * @When I enter :value as value for the constraint with customer option :customerOption
     */
    public function iEnterAsTheConstraintsValue($value, CustomerOptionInterface $customerOption)
    {
        $value = $this->prepareValue($value, $customerOption->getType());

        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setConstraintValue($value, $customerOption->getType());
    }

    /**
     * Prepares a value for comparision (resolves arrays etc.)
     *
     * @param        $value
     * @param string $optionType
     *
     * @return array|mixed|null|string|string[]
     */
    private function prepareValue($value, string $optionType)
    {
        if (is_string($value)) {
            $value = preg_replace('/\s+/', '', $value);

            if (CustomerOptionTypeEnum::isSelect($optionType)) {
                $value = explode(',', $value);
            } elseif ($optionType === CustomerOptionTypeEnum::BOOLEAN) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $value;
    }

    /**
     * @Then the customer option group :customerOptionGroup should have :conditionType:
     */
    public function theCustomerOptionGroupShouldHaveAValidator(
        CustomerOptionGroupInterface $customerOptionGroup,
        string $conditionType,
        TableNode $table
    ) {
        /** @var ConditionInterface[] $conditionsToCheck */
        $conditionsToCheck = array_map(
            function (ValidatorInterface $validator) use ($conditionType): array {
                switch ($conditionType) {
                    case 'conditions':
                        return $validator->getConditions()->toArray();
                    case 'constraints':
                        return $validator->getConstraints()->toArray();
                }

                throw new InvalidArgumentException('The condition type has to be either conditions or constraints');
            },
            $customerOptionGroup->getValidators()
        );

        $flatConditionsToCheck = [];
        foreach ($conditionsToCheck as $conditionsArray) {
            $flatConditionsToCheck = array_merge($flatConditionsToCheck, $conditionsArray);
        }

        $result = false;

        foreach ($table->getHash() as $row) {
            foreach ($flatConditionsToCheck as $condition) {
                $customerOption = $condition->getCustomerOption();

                if ($customerOption->getName() == $row['option']) {
                    $val = $this->prepareValue($row['value'], $customerOption->getType());

                    $sameComp = $condition->getComparator() == $row['comparator'];
                    $sameVal  = $this->values_are_equal(
                        $condition->getValue()['value'], $val, $customerOption->getType()
                    );

                    if ($sameComp && $sameVal) {
                        $expectedMessage = $row['error_message'];
                        $result = $condition->getValidator()->getErrorMessage()->getMessage() === $expectedMessage;
                    }
                }
            }
        }

        Assert::true($result, 'The validator does not contain the condition');
    }

    private function values_are_equal($a, $b, string $optionType)
    {
        if (CustomerOptionTypeEnum::isSelect($optionType)) {
            $result = (
                is_array($a) && is_array($b)
                && array_diff($a, $b) === array_diff($b, $a)
            );
        } elseif (CustomerOptionTypeEnum::isDate($optionType)) {
            $a = new \DateTime($a['date']);
            $b = new \DateTime($b);

            $result = $a == $b;
        } else {
            $result = $a == $b;
        }

        return $result;
    }

    /**
     * @When I define the validators error message as :message
     */
    public function iDefineTheValidatorsErrorMessageAs($message)
    {
        /** @var CreatePage|UpdatePage $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setErrorMessage($message);
    }
}
