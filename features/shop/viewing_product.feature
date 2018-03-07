@shop
@customer_options
Feature: Viewing products with customer options
    In order to configure a product
    As a customer
    I want to see the available customer options

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option "select_option" named "Select Option"
        And I have a customer option "text_option" named "Text Option"
        And I have a customer option "date_option" named "Date Option"
        And I have a customer option "number_option" named "Number Option"

        And I have a customer option group "some_group" named "Some Group"
        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Number Option"

        And the store has a product "Cool Product"
        And product "Cool Product" has the customer option group "Some Group"

    @ui
    Scenario: Viewing a product with customer options
        When I view product "Cool Product"
        Then I should see customization for "Select Option"
        And I should see customization for "Text Option"
        And I should see customization for "Date Option"
        And I should see customization for "Number Option"