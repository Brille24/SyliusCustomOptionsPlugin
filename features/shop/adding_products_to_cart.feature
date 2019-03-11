@shop
@customer_options
@ui
@javascript
Feature: Adding products with customer options to cart
    In order to buy a configured product
    As a customer
    I want to be able to add it to the cart and have the options validated

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option named "Select Option" with type "select"
        And customer option "Select Option" has a value "Value 1"
        And customer option "Select Option" has a value "Value 2"
        And customer option "Select Option" has a value named "Value 3" in "en_US" priced 5

        And I have a customer option named "Text Option" with type "text"
        And I have a customer option named "Date Option" with type "date"
        And I have a customer option named "Number Option" with type "number"

        And I have a customer option group named "Some Group"
        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Number Option"

        And the store has a product "Cool Product" priced at "$10.00"
        And product "Cool Product" has the customer option group "Some Group"

    Scenario: Adding a product without required customer options to the cart
        Given I view product "Cool Product"
        When I enter value "some text" for customer option "Text Option"
        And I select value "Value 1" for customer option "Select Option"
        And I enter value "15" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with unit price "$10.00" in my cart
        And my cart's total should be "$10.00"

    Scenario: Adding a product with a priced value to the cart
        Given I view product "Cool Product"
        When I enter value "some text" for customer option "Text Option"
        And I select value "Value 3" for customer option "Select Option"
        And I enter value "42" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with unit price "$15.00" in my cart
        And my cart's total should be "$15.00"

    Scenario: Trying to add a product with unfilled required customer options to the cart
        Given customer option "Number Option" is required
        And I view product "Cool Product"
        When I enter no value for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option is required
        And my cart should be empty

    Scenario: Trying to add a product with filled required customer options to the cart
        Given customer option "Number Option" is required
        And I view product "Cool Product"
        When I enter value "42" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with quantity 1 in my cart

    Scenario: Trying to add a product with invalid customer option input to the cart
        Given I view product "Cool Product"
        When I enter value "not a number" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option is invalid
        And my cart should be empty

    Scenario: Trying to add a product with customer option value not meeting constraint
        Given customer option "Number Option" has constraint 0 to 10
        And I view product "Cool Product"
        When I enter value "20" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint
        And my cart should be empty
