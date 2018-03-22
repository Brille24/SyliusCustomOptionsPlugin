<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Context\Admin;


use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator\Condition;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConditionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
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

    private function prepareValue($value, $optionType){
        if(is_string($value)){
            $value = preg_replace('/\s+/', '', $value);

            if(CustomerOptionTypeEnum::isSelect($optionType)){
                $value = explode(',', $value);
            }elseif ($optionType === CustomerOptionTypeEnum::BOOLEAN){
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $value;
    }

    /**
     * @Then the customer option group :customerOptionGroup should have a validator:
     */
    public function theCustomerOptionGroupShouldHaveAValidator(CustomerOptionGroupInterface $customerOptionGroup, TableNode $table)
    {
        $validatorConfig = $table->getHash();

        $errorMessage = str_replace('"', '', $validatorConfig[0]['error_message']);

        $hasValidator = false;

        /** @var ValidatorInterface $validator */
        foreach ($customerOptionGroup->getValidators() as $validator){
            /** @var ConditionInterface[] $conditions */
            $conditions = $validator->getConditions();

            /** @var ConditionInterface[] $conditions */
            $constraints = $validator->getConstraints();

            $result = true;

            foreach ($validatorConfig as $row){

                foreach ($row as &$value){
                    $value = str_replace('"', '', $value);
                }
                $row['condition_value'] = strtolower($row['condition_value']);
                $row['constraint_value'] = strtolower($row['constraint_value']);

                $hasCondition = $hasConstraint = true;

                // Check for condition
                if(!empty($row['condition_option'])) {

                    $hasCondition = false;

                    foreach ($conditions as $condition) {
                        $customerOption = $condition->getCustomerOption();

                        if($customerOption->getName() == $row['condition_option']){
                            $val = $this->prepareValue($row['condition_value'], $customerOption->getType());

                            $sameComp = $condition->getComparator() == $row['condition_comparator'];
                            $sameVal = $this->values_are_equal($condition->getValue()['value'], $val, $customerOption->getType());

                            if($sameComp && $sameVal){
                                $hasCondition = true;
                                break;
                            }
                        }
                    }
                }

                //Check for constraint
                if(!empty($row['constraint_option'])) {

                    $hasConstraint = false;

                    foreach ($constraints as $constraint) {
                        $customerOption = $constraint->getCustomerOption();

                        if($customerOption->getName() == $row['constraint_option']){
                            $val = $this->prepareValue($row['constraint_value'], $customerOption->getType());

                            $sameComp = $constraint->getComparator() == $row['constraint_comparator'];
                            $sameVal = $this->values_are_equal($constraint->getValue()['value'], $val, $customerOption->getType());

                            if($sameComp && $sameVal){
                                $hasConstraint = true;
                                break;
                            }
                        }
                    }
                }

                if(!$hasCondition || !$hasConstraint){
                    $result = false;
                    break;
                }
            }

            if($result){
                if(empty($errorMessage)) {
                    $hasValidator = true;
                }else{
                    $hasValidator = $validator->getErrorMessage()->getMessage() === $errorMessage;
                }
            }
        }

        Assert::true($hasValidator);
    }


    private function values_are_equal($a, $b, string $optionType){
        if(CustomerOptionTypeEnum::isSelect($optionType)){
            $result = (
                is_array($a) && is_array($b)
                && array_diff($a, $b) === array_diff($b, $a)
            );

        }elseif(CustomerOptionTypeEnum::isDate($optionType)){
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
