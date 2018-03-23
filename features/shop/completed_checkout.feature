@shop
@customer_options
Feature: Viewing products after completed checkout
    In order to know what options I chose and how they affect the price
    As a customer
    I want to see my selected options in the order summary

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option named "Select Option" with type "select"
        And customer option "Select Option" has a value named "Value 1" in "en_US" priced 15
        And customer option "Select Option" has a value named "Value 2" in "en_US" priced 20
        And customer option "Select Option" has a value named "Value 3" in "en_US" priced 42

        And I have a customer option named "Text Option" with type "text"
        And I have a customer option named "Date Option" with type "date"
        And I have a customer option named "Number Option" with type "number"
        And I have a customer option named "Boolean Option" with type "boolean"
        And I have a customer option named "Other Boolean Option" with type "boolean"

        And I have a customer option group named "Some Group"
        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Number Option"
        And customer option group "Some Group" has option "Boolean Option"
        And customer option group "Some Group" has option "Other Boolean Option"

        And the store has a product "Cool Product"
        And product "Cool Product" has the customer option group "Some Group"

        And the store ships everywhere for free
        And the store allows paying offline

        And I am a logged in customer

    @ui @javascript
    Scenario: Completing checkout with product with customer options
        Given I view product "Cool Product"
        When I select value "Value 3" for customer option "Select Option"
        And I enter value "My text" for customer option "Text Option"
        And I enter value "2013-08-05" for customer option "Date Option"
        And I enter value "123" for customer option "Number Option"
        And I enter value "true" for customer option "Boolean Option"
        And I enter value "false" for customer option "Other Boolean Option"
        And I add it to the cart

        And I proceed through checkout process
        Then I should be on the checkout summary step

        And I should see "Customer Options"
        And I should see "Select Option: Value 3"
        And I should see "Text Option: My text"
        And I should see "Date Option: 5"
        And I should see "Date Option: 8"
        And I should see "Date Option: 2013"
        And I should see "Number Option: 123"
        And I should see "Boolean Option: 1"
        And I should not see "Other Boolean Option"