@products
@admin
Feature: Managing CustomerOption on Products
    In order to manage customer options for a product
    As an Administrator
    I want to assign groups and change value prices

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Some Product"
        And I have a customer option group "some_group" named "Some Group"
        And I have a customer option "number_option" named "Number Option"
        And I have a customer option "select_option" named "Select Option"
        And I am logged in as an administrator

    @ui
    Scenario: Assigning a group to a product
        Given I want to modify the "Some Product" product
        When I choose customer option group "Some Group"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And product "Some Product" should have customer option group "Some Group"