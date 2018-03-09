@shop
@customer_options
Feature: Viewing products after completed checkout
    In order to know what options I chose and how they affect the price
    As a customer
    I want to see my selected options in the order summary

    Background:
        Given the store operates on a single channel in "United States"
        And I have a customer option "select_option" named "Select Option" with type "select"
        And customer option "Select Option" has a value named "Value 1" in "en_US" priced 15
        And customer option "Select Option" has a value named "Value 2" in "en_US" priced 20
        And customer option "Select Option" has a value named "Value 3" in "en_US" priced 42

        And I have a customer option "text_option" named "Text Option" with type "text"
        And I have a customer option "date_option" named "Date Option" with type "date"
        And I have a customer option "number_option" named "Number Option" with type "number"
        And I have a customer option "file_option" named "File Option" with type "file"
        And I have a customer option "boolean_option" named "Boolean Option" with type "boolean"

        And I have a customer option group "some_group" named "Some Group"
        And customer option group "Some Group" has option "Select Option"
        And customer option group "Some Group" has option "Text Option"
        And customer option group "Some Group" has option "Date Option"
        And customer option group "Some Group" has option "Number Option"
#        And customer option group "Some Group" has option "File Option"
        And customer option group "Some Group" has option "Boolean Option"

        And the store has a product "Cool Product"
        And product "Cool Product" has the customer option group "Some Group"

        And the store ships everywhere for free for all channels
        And the store allows paying with "Cash on Delivery"

        And I am a logged in customer

    @ui

    Scenario: Completing checkout with product with customer options
        Given I have 1 products "Cool Product" in the cart

        And I chose value "Value 3" for option "Select Option" for this order
        And I entered value "My text" for option "Text Option" for this order
        And I entered value "1990-08-05" for option "Date Option" for this order
        And I entered value "123" for option "Number Option" for this order
#        And I entered value "images/my_image.jpg" for option "File Option" for this order
        And I entered value "true" for option "Boolean Option" for this order

        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Eddard Stark"
        And I complete the addressing step
        And I proceed with "Free" shipping method
        Then I should be on the checkout summary step

        And I should see "Customer Options"
        And I should see "Select Option: Value 3"
        And I should see "Text Option: My text"
        And I should see "Date Option: 05"
        And I should see "Date Option: 08"
        And I should see "Date Option: 1990"
        And I should see "Number Option: 123"
#        And I should see "File Option: images/my_image.jpg"
        And I should see "Boolean Option: true"