@shop
@customer_options
Feature: Viewing products with customer options
    In order to configure a product
    As a customer
    I want to see the available customer options

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option named "Select Option" with type "select"
        And customer option "Select Option" has a value "Value 1"
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
    @javascript
    Scenario: Viewing a product with customer options
        When I view product "Cool Product"
        And I should see customization for "Text Option"
        Then I should see customization for "Select Option"
        And I should see customization for "Date Option"
        And I should see customization for "Number Option"
        And I should see customization for "File Option"
