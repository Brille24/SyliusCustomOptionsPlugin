@admin
@customer_options
Feature: Managing CustomerOptions
    In order to make products configurable by the customer
    As an Administrator
    I want to create, edit and delete CustomerOptions

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And I have a customer option named "Some Option"

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
    Scenario: Editing a CustomerOption
        Given I want to edit customer option "Some Option"
        When I specify its type as "Number"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And I should see configuration for "Maximum number"
        And I should see configuration for "Minimum number"

    @ui
    Scenario: Deleting a CustomerOption
        Given I want to browse customer options
        When I delete customer option "Some Option"
        Then I should be notified that it has been successfully deleted
        And the customer option "Some Option" should not appear in the registry

    @ui
    Scenario: Trying to create a CustomerOption with existing code
        Given I want to create a new customer option
        And I specify its code as "some_option"
        And I name it "Another Option"
        When I add it
        Then I should be notified that code has to be unique
        And the customer option "Another Option" should not appear in the registry
