@shop
@customer_options
@validator
@constraints
@javascript
Feature: Validating with programmable constraints
    In order to not enter invalid value combinations
    As a customer
    I want to be bound by conditional constraints

    Background:
        Given the store operates on a single channel in "United States"

        And I have a customer option group named "Some Group"

        And I have a customer option named "Select Option" with type "select"
        And customer option "Select Option" has a value "Value-1"
        And customer option "Select Option" has a value "Value-2"
        And customer option "Select Option" has a value "Value-3"
        And customer option "Select Option" has a value "Value-4"

        And I have a customer option named "Text Option" with type "text"
        And I have a customer option named "Number Option" with type "number"
        And I have a customer option named "Date Option" with type "date"
        And I have a customer option named "Boolean Option" with type "boolean"

        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Number Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Boolean Option"

        And customer option group "Some Group" has a validator:
            |condition_option|condition_comparator|condition_value |constraint_option|constraint_comparator|constraint_value|error_message|
            |Select Option   |in_set              |Value-1, Value-3|Text Option      |greater              |2               |The awesome constraint failed.|
            |Number Option   |lesser              |50.5            |Boolean Option   |equal                |true            |                              |
            |Date Option     |equal               |2000-1-1        |                 |                     |                |                              |

        And the store has a product "Cool Product" priced at "$15"
        And product "Cool Product" has the customer option group "Some Group"

        And I am a logged in customer

    @ui
    Scenario Outline: Meeting conditions and constraints
        Given I view product "Cool Product"

        When I enter value <select> for customer option "Select Option"
        And I enter value <number> for customer option "Number Option"
        And I enter value <date> for customer option "Date Option"

        And I enter value <text> for customer option "Text Option"
        And I enter value <bool> for customer option "Boolean Option"

        And I add it to the cart

        Then I should be notified that the product has been successfully added
        And I should see "Cool Product" with quantity 1 in my cart

        Examples:
            |select     |number |date       |text        |bool   |
            |"Value-1"  |"25.25"|"2000-1-1" |"somebody"  |"true" |
            |"Value-3"  |"8"    |"2000-1-1" |"once"      |"true" |
            |"Value-1"  |"50.49"|"2000-1-1" |"told me"   |"true" |

    @ui
    Scenario Outline: Meeting conditions but not constraints
        Given I view product "Cool Product"

        When I enter value <select> for customer option "Select Option"
        And I enter value <number> for customer option "Number Option"
        And I enter value <date> for customer option "Date Option"

        And I enter value <text> for customer option "Text Option"
        And I enter value <bool> for customer option "Boolean Option"

        And I add it to the cart

        Then I should be notified that the validation failed
        And my cart should be empty

        Examples:
            |select     |number |date       |text  |bool   |
            |"Value-1"  |"25.25"|"2000-1-1" |"so"  |"false"|
            |"Value-3"  |"8"    |"2000-1-1" |"o"   |"true" |
            |"Value-1"  |"50.49"|"2000-1-1" |"told"|"false"|

    @ui
    Scenario Outline: Not meeting conditions but constraints
        Given I view product "Cool Product"

        When I enter value <select> for customer option "Select Option"
        And I enter value <number> for customer option "Number Option"
        And I enter value <date> for customer option "Date Option"

        And I enter value <text> for customer option "Text Option"
        And I enter value <bool> for customer option "Boolean Option"

        And I add it to the cart

        Then I should be notified that the product has been successfully added

        Examples:
            |select     |number |date       |text        |bool   |
            |"Value-2"  |"25.25"|"2000-1-1" |"somebody"  |"true" |
            |"Value-3"  |"51"   |"2000-1-1" |"once"      |"true" |
            |"Value-1"  |"50.49"|"2018-1-1" |"told me"   |"true" |

    @ui
    Scenario Outline: Not meeting conditions and constraints
        Given I view product "Cool Product"

        When I enter value <select> for customer option "Select Option"
        And I enter value <number> for customer option "Number Option"
        And I enter value <date> for customer option "Date Option"

        And I enter value <text> for customer option "Text Option"
        And I enter value <bool> for customer option "Boolean Option"

        And I add it to the cart

        Then I should be notified that the product has been successfully added

        Examples:
            |select     |number |date       |text  |bool   |
            |"Value-2"  |"25.25"|"2000-1-1" |"so"  |"false"|
            |"Value-3"  |"51"   |"2000-1-1" |"o"   |"true" |
            |"Value-1"  |"50.49"|"2018-1-1" |"told"|"false"|