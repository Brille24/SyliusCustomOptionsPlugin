@admin
@customer_options
Feature: Checking if configuration is available
    In order to limit the values the customer can enter for an option
    As an Administrator
    I want to configure constraints

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And I want to create a new customer option
        And I specify its code as "cool_option"
        And I name it "Cool Option"

    @ui
    Scenario: Creating a new text option
        Given I specify its type as "Text"
        And I add it
        Then I should see configuration for "Maximum length"
        And I should see configuration for "Minimum length"

    @ui
    Scenario: Creating a new date option
        Given I specify its type as "Date"
        And I add it
        Then I should see configuration for "Maximum date"
        And I should see configuration for "Minimum date"

    @ui
    Scenario: Creating a new file option
        Given I specify its type as "File"
        And I add it
        Then I should see configuration for "Maximum size"
        Then I should see configuration for "Minimum size"
    @ui
    Scenario: Creating a new datetime option
        Given I specify its type as "Datetime"
        And I add it
        Then I should see configuration for "Maximum date"
        And I should see configuration for "Minimum date"

    @ui
    Scenario: Creating a new number option
        Given I specify its type as "Number"
        And I add it
        Then I should see configuration for "Maximum number"
        And I should see configuration for "Minimum number"

    @ui
    Scenario: Creating a new boolean option
        Given I specify its type as "Boolean"
        And I add it
        Then I should see configuration for "Default value"

    @ui
    Scenario: Creating a new select option
        Given I specify its type as "Select"
        And I add it
        Then I should see a link "Add"

    @ui
    @javascript
    Scenario: Creating a new select option with values
        Given I specify its type as "Select"
        And I add it
        And I want to edit customer option "Cool Option"
        And I add a value "val_1" with name "Value 1"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And I should see price configuration for value "val_1"
