@managing_customer_options
Feature: Creating a new CustomerOption
    In order to make products configurable by the customer
    As an Administrator
    I want to create new CustomerOptions

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Creating a new CustomerOption
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Customer Option"
        And I set it required
        And I add it
        Then I should be notified that it has been successfully created
        And the customer option "Cool Customer Option" should appear in the registry

    @ui
    Scenario: Creating a new text option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Text"
        And I add it
        Then I should see configuration for "Maximum length"
        And I should see configuration for "Minimum length"

    @ui
    Scenario: Creating a new select option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Select"
        And I add it
        Then I should see a link "Add"

    @ui
    @javascript
    Scenario: Creating a new select option with values
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Select"
        And I add it
        And I add a value "val_1" with name "Value 1"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And I should see price configuration for value "Value 1"

    @ui
    Scenario: Creating a new file option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "File"
        And I add it
        Then I should see configuration for "Maximum size"
        And I should see configuration for "Minimum size"

    @ui
    Scenario: Creating a new date option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Date"
        And I add it
        Then I should see configuration for "Maximum date"
        And I should see configuration for "Minimum date"

    @ui
    Scenario: Creating a new datetime option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Datetime"
        And I add it
        Then I should see configuration for "Maximum date"
        And I should see configuration for "Minimum date"

    @ui
    Scenario: Creating a new number option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Number"
        And I add it
        Then I should see configuration for "Maximum number"
        And I should see configuration for "Minimum number"

    @ui
    Scenario: Creating a new boolean option
        Given I want to create a new customer option
        When I specify its code as "cool_option"
        And I name it "Cool Option"
        And I specify its type as "Boolean"
        And I add it
        Then I should see configuration for "Default value"