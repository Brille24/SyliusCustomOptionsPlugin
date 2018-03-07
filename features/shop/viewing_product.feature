@shop
@customer_options
Feature: Viewing products with customer options
    In order to configure a product
    As a customer
    I want to see the available customer options

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option "select_option" named "Select Option" with type "select"
        And customer option "Select Option" has a value "val_1"
        And customer option "Select Option" has a value "val_2"
        And customer option "Select Option" has a value "val_3"

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
    Scenario: Viewing a product with customer options
        When I view product "Cool Product"
        And I should see customization for "Text Option"
        Then I should see customization for "Select Option"
        And I should see customization for "Date Option"
        And I should see customization for "Number Option"

    @ui
    Scenario: Adding a product without required customer options to the cart
        Given I view product "Cool Product"
        When I add product "Cool Product" to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with quantity 1 in my cart

    @ui
    @javascript
    Scenario: Adding a product with required customer options to the cart
        Given customer option "Number Option" is required
        And I view product "Cool Product"
        And I should see customization for "Number Option"
        When I enter no value for customer option "Number Option"
        And I add product "Cool Product" to the cart
        And customer option "Select Option" is required
        Then I should have 0 "Cool Product" products in the cart