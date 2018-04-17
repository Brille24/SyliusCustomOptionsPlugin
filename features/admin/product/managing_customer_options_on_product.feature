@products
@admin
Feature: Managing CustomerOption on Products
    In order to manage customer options for a product
    As an Administrator
    I want to assign groups and change value prices

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Some Product"

        And I have a customer option named "Number Option" with type "number"
        And I have a customer option named "Select Option" with type "select"

        And customer option "Select Option" has a value named "Value 1" in "en_US" priced 5
        And customer option "Select Option" has a value named "Value 2" in "en_US" priced 10

        And I have a customer option group named "Some Group"
        And customer option group "Some Group" has option "Number Option"
        And customer option group "Some Group" has option "Select Option"

        And I am logged in as an administrator

    @ui
    @javascript
    Scenario: Assigning a group to an existing product
        Given I want to modify the "Some Product" product
        When I choose customer option group "Some Group"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And product "Some Product" should have customer option group "Some Group"

    @ui
    @javascript
    Scenario: Assigning a group to a new product
        Given I want to create a new simple product
        When I specify its code as "new_product"
        And I name it "New Product" in "en_US"
        And I choose customer option group "Some Group"
        And I add it
        Then I should be notified that it has been successfully created
        And product "New Product" should have customer option group "Some Group"

    @ui
    @javascript
    Scenario: Overriding value prices for a product
        Given product "Some Product" has the customer option group "Some Group"
        And I want to modify the "Some Product" product
        When I add a new customer option value price
        And I select customer option value "Value 1"
        And I set amount to 15
        And I set type to "Fixed amount"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And product "Some Product" should have customer option group "Some Group"
        And product "Some Product" should have a customer option value price with amount 15