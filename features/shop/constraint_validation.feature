@shop
@customer_options
@constraints
@javascript
Feature: Validating submitted customer options using constraints
    In order to make sure the submitted data is within the allowed set of values
    As a customer
    I want to only get the product added to the cart, when the values are matching the constraint

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option group named "Some Group"

        And the store has a product "Customizable Product" priced at "$15.00"
        And product "Customizable Product" has the customer option group "Some Group"

    @ui
    Scenario: Validating a text
        Given I have a customer option named "Text Option" with type "text"
        And customer option "Text Option" has constraint 5 to 10
        And customer option group "Some Group" has option "Text Option"
        And product "Customizable Product" has the customer option group "Some Group"

        When I view product "Customizable Product"
        And I enter value "abc" for customer option "Text Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "abcdefghijklmnop" for customer option "Text Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "valid text" for customer option "Text Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added

    @ui
    Scenario: Validating a number
        Given I have a customer option named "Number Option" with type "number"
        And customer option "Number Option" has constraint "-5" to "55"
        And customer option group "Some Group" has option "Number Option"
        And product "Customizable Product" has the customer option group "Some Group"

        When I view product "Customizable Product"
        And I enter value "-10" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "666" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "42" for customer option "Number Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added

    @ui
    Scenario: Validating a date
        Given I have a customer option named "Date Option" with type "date"
        And customer option "Date Option" has constraint "1999-1-1" to "2030-12-31"
        And customer option group "Some Group" has option "Date Option"
        And product "Customizable Product" has the customer option group "Some Group"

        When I view product "Customizable Product"
        And I enter value "1900-5-5" for customer option "Date Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "2076-11-9" for customer option "Date Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "2018-3-14" for customer option "Date Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added

    @ui
    Scenario: Validating a datetime
        Given I have a customer option named "Datetime Option" with type "datetime"
        And customer option "Datetime Option" has constraint "1999-1-1 00:00:00" to "2030-12-31 23:59:59"
        And customer option group "Some Group" has option "Datetime Option"
        And product "Customizable Product" has the customer option group "Some Group"

        When I view product "Customizable Product"
        And I enter value "1900-5-5 12:45:00" for customer option "Datetime Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "2076-11-9 13:32:10" for customer option "Datetime Option"
        And I add it to the cart
        Then I should be notified that an option does not meet a constraint

        When I enter value "2018-3-14 05:06:07" for customer option "Datetime Option"
        And I add it to the cart
        Then I should be notified that the product has been successfully added