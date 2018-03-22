@admin
@customer_option_groups
Feature: Managing CustomerOptionGroups
    In order to group CustomerOptions together
    As an Administrator
    I want to create CustomerOptionGroups

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator
        And I have a customer option named "Option 1"
        And I have a customer option named "Option 2"
        And I have a customer option named "Option 3"
        And I have a customer option group named "Group 1"

    @ui
    Scenario: Creating a new CustomerOptionGroup
        Given I want to create a new customer option group
        When I specify its code as "some_group"
        And I name it "Some Group"
        And I add it
        Then I should be notified that it has been successfully created
        And the customer option group "Some Group" should appear in the registry

    @ui
    Scenario: Editing a CustomerOptionGroup
        Given I want to edit customer option group "Group 1"
        When I name it "Group 2"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer option group "Group 2" should appear in the registry
    @ui
    Scenario: Deleting a CustomerOptionGroup
        Given I want to browse customer option groups
        When I delete customer option group "Group 1"
        Then I should be notified that it has been successfully deleted
        And the customer option group "Group 1" should not appear in the registry

    @ui
    @javascript
    Scenario: Adding options to a new group
        Given I want to create a new customer option group
        When I specify its code as "some_group"
        And I name it "Some Group"
        And I add a customer option "Option 1"
        And I add a customer option "Option 2"
        And I add it
        Then I should be notified that it has been successfully created
        And the customer option group "Some Group" should appear in the registry
        And the customer option group "Some Group" should have option "Option 1"
        And the customer option group "Some Group" should have option "Option 2"

    @ui
    @javascript
    Scenario: Adding options to an existing group
        Given I want to edit customer option group "Group 1"
        When I add a customer option "Option 3"
        And I add a customer option "Option 1"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer option group "Group 1" should have option "Option 3"
        And the customer option group "Group 1" should have option "Option 1"