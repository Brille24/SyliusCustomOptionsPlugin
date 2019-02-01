@shop
@customer_options
Feature: Viewing product with customer options in cart
    In order to know what options I chose and how they affect the price
    As a customer
    I want to see the chosen options and their pricing in the cart summary

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option named "Select Option" with type "select"
        And customer option "Select Option" has a value named "Value 1" in "en_US" priced 15
        And customer option "Select Option" has a value "Value 2"
        And customer option "Select Option" has a value "Value 3"

        And I have a customer option named "Text Option" with type "text"
        And I have a customer option named "Date Option" with type "date"
        And I have a customer option named "Number Option" with type "number"
        And I have a customer option named "File Option" with type "file"

        And I have a customer option group named "Some Group"
        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Number Option"
        And customer option group "Some Group" has option "File Option"

        And the store has a product "Cool Product"
        And product "Cool Product" has the customer option group "Some Group"

    @ui
    Scenario: Having a product with options in the cart
        Given I have 1 products "Cool Product" in the cart
        And I chose value "Value 1" for option "Select Option" for this order
        And I entered value "Custom text" for option "Text Option" for this order
        And I entered value "2018-06-18" for option "Date Option" for this order
        And I entered value "42" for option "Number Option" for this order
        And I entered value "/hello" for option "File Option" for this order
        When I see the summary of my cart
        Then I should see "Customer Options"
        And I should see "Select Option: Value 1"
        And I should see "Text Option: Custom text"
        And I should see "Date Option: 18"
        And I should see "Date Option: 06"
        And I should see "Date Option: 2018"
        And I should see "Number Option: 42"
        And I should see "File Option: File content"

    @ui
    Scenario: Having a product without options in the cart
        Given the store has a product "Boring Product"
        And I have 1 products "Boring Product" in the cart
        When I see the summary of my cart
        Then I should not see "Customer Options"
