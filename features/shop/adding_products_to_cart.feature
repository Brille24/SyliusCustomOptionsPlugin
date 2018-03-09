@shop
@customer_options
Feature: Adding products with customer options to cart
    In order to buy a configured product
    As a customer
    I want to be able to add it to the cart and have the options validated

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option "select_option" named "Select Option" with type "select"
        And customer option "Select Option" has a value "Value 1"
        And customer option "Select Option" has a value "Value 2"
        And customer option "Select Option" has a value "Value 3"

        And I have a customer option "text_option" named "Text Option" with type "text"
        And I have a customer option "date_option" named "Date Option" with type "date"
        And I have a customer option "number_option" named "Number Option" with type "number"

        And I have a customer option group "some_group" named "Some Group"
        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Number Option"

        And the store has a product "Cool Product"
        And product "Cool Product" has the customer option group "Some Group"

    @ui
    @javascript
    Scenario: Adding a product without required customer options to the cart
        Given I view product "Cool Product"
        When I enter value "some text" for customer option "Text Option"
        When I select value "Value 1" for customer option "Select Option"
        When I enter value "15" for customer option "Number Option"
        When I add product "Cool Product" to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with quantity 1 in my cart

    @ui
    @javascript
    Scenario: Trying to add a product with unfilled required customer options to the cart
        Given customer option "Number Option" is required
        And I view product "Cool Product"
        When I enter no value for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option is required
        And my cart should be empty

    @ui
    @javascript
    Scenario: Trying to add a product with filled required customer options to the cart
        Given customer option "Number Option" is required
        And I view product "Cool Product"
        When I enter value "42" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with quantity 1 in my cart

    @ui
    @javascript
    Scenario: Trying to add a product with invalid customer option input to the cart
        Given I view product "Cool Product"
        When I enter value "not a number" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option is invalid
        And my cart should be empty

    @ui
    @javascript
        @todo
    Scenario: Trying to add a product with customer option value not meeting constraint
        Given customer option "Number Option" has constraint 0 to 10
        And I view product "Cool Product"
        When I enter value "20" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint
        And my cart should be empty