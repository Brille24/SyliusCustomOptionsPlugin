@admin
@customer_option_groups
@validator
@constraints
@javascript
Feature: Configuring validators
    In order to limit the accepted values of an option based on another option
    As an Administrator
    I want to be able to assign validators with conditional constraints to a group

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

        And I am logged in as an administrator

    @ui
    Scenario: Adding and deleting validators
        Given I want to edit customer option group "Some Group"
        When I add a validator
        Then I should see 1 validators

        When I add a validator
        And I add a validator
        Then I should see 3 validators

        When I delete a validator
        Then I should see 2 validators

        When I delete a validator
        Then I should see 1 validators

        When I delete a validator
        Then I should see 0 validators

    @ui
    Scenario Outline: The condition value type and available comparators change based on customer option type
        Given I want to edit customer option group "Some Group"
        When I add a validator
        And I add a condition
        And I pick <option> as the conditions customer option
        Then the conditions value with customer option type <option_type> should be of type <type>
        And the conditions available comparators should be <comparators>

        Examples:
            | option           | option_type | type       | comparators                                           |
            | "Text Option"    | "text"      | "number"   | "greater, greater_equal, equal, lesser_equal, lesser" |
            | "Select Option"  | "select"    | "select"   | "in_set, not_in_set"                                  |
            | "Number Option"  | "number"    | "text"     | "greater, greater_equal, equal, lesser_equal, lesser" |
            | "Date Option"    | "date"      | "div"      | "greater, greater_equal, equal, lesser_equal, lesser" |
            | "Boolean Option" | "boolean"   | "checkbox" | "equal"                                               |

    @ui
    Scenario Outline: The constraint value type and available comparators change based on customer option type
        Given I want to edit customer option group "Some Group"
        When I add a validator
        And I add a constraint
        And I pick <option> as the constraints customer option
        Then the constraints value with customer option type <option_type> should be of type <type>
        And the constraints available comparators should be <comparators>

        Examples:
            | option           | option_type | type       | comparators                                           |
            | "Text Option"    | "text"      | "number"   | "greater, greater_equal, equal, lesser_equal, lesser" |
            | "Select Option"  | "select"    | "select"   | "in_set, not_in_set"                                  |
            | "Number Option"  | "number"    | "text"     | "greater, greater_equal, equal, lesser_equal, lesser" |
            | "Date Option"    | "date"      | "div"      | "greater, greater_equal, equal, lesser_equal, lesser" |
            | "Boolean Option" | "boolean"   | "checkbox" | "equal"                                               |

    @ui
    Scenario Outline: Single conditions are saved correctly
        Given I want to edit customer option group "Some Group"

        When I add a validator
        And I add a condition
        And I pick <option_name> as the conditions customer option
        And I select <comparator> as the conditions comparator
        And I enter <value> as value for the condition with customer option <option_name>
        And I define the validators error message as <error_message>
        And I save my changes

        Then the customer option group "Some Group" should have conditions:
            | option        | comparator   | value   | error_message   |
            | <option_name> | <comparator> | <value> | <error_message> |

        Examples:
            | option_name      | comparator      | value                       | error_message           |
            | "Text Option"    | "lesser"        | 4                           | "Something went wrong!" |
            | "Select Option"  | "not_in_set"    | "Value-1, Value-2, Value-4" | "Abc"                   |
            | "Number Option"  | "equal"         | 5.5                         | "Def"                   |
            | "Date Option"    | "greater_equal" | "2018-10-15"                | "Ravioli"               |
            | "Boolean Option" | "equal"         | true                        | "Spaghetti"             |

    @ui
    Scenario Outline: Single constraints are saved correctly
        Given I want to edit customer option group "Some Group"

        When I add a validator
        And I add a constraint
        And I pick <option_name> as the constraints customer option
        And I select <comparator> as the constraints comparator
        And I enter <value> as value for the constraint with customer option <option_name>
        And I define the validators error message as <error_message>
        And I save my changes

        Then the customer option group "Some Group" should have constraints:
            | option        | comparator   | value   | error_message   |
            | <option_name> | <comparator> | <value> | <error_message> |

        Examples:
            | option_name      | comparator      | value                       | error_message           |
            | "Text Option"    | "lesser"        | 4                           | "Something went wrong!" |
            | "Select Option"  | "not_in_set"    | "Value-1, Value-2, Value-4" | "Abc"                   |
            | "Number Option"  | "equal"         | 5.5                         | "Def"                   |
            | "Date Option"    | "greater_equal" | "2018-10-15"                | "Ravioli"               |
            | "Boolean Option" | "equal"         | true                        | "Spaghetti"             |

    @ui
    Scenario: Multiple conditions are saved correctly
        Given I want to edit customer option group "Some Group"

        When I add a validator
        And I define the validators error message as "Oops"

        And I add a condition
        And I pick "Text Option" as the conditions customer option
        And I select "equal" as the conditions comparator
        And I enter 5 as value for the condition with customer option "Text Option"

        And I add a condition
        And I pick "Select Option" as the conditions customer option
        And I select "in_set" as the conditions comparator
        And I enter "Value-1, Value-3" as value for the condition with customer option "Select Option"

        And I add a condition
        And I pick "Boolean Option" as the conditions customer option
        And I select "equal" as the conditions comparator
        And I enter true as value for the condition with customer option "Boolean Option"

        And I save my changes

        Then the customer option group "Some Group" should have conditions:
            | option         | comparator | value            | error_message |
            | Text Option    | equal      | 5                | Oops          |
            | Select Option  | in_set     | Value-1, Value-3 | Oops          |
            | Boolean Option | equal      | true             | Oops          |

    @ui
    Scenario: Multiple constraints are saved correctly
        Given I want to edit customer option group "Some Group"

        When I add a validator
        And I define the validators error message as "Oops"

        And I add a constraint
        And I pick "Text Option" as the constraints customer option
        And I select "equal" as the constraints comparator
        And I enter 5 as value for the constraint with customer option "Text Option"

        And I add a constraint
        And I pick "Select Option" as the constraints customer option
        And I select "in_set" as the constraints comparator
        And I enter "Value-1, Value-3" as value for the constraint with customer option "Select Option"

        And I add a constraint
        And I pick "Boolean Option" as the constraints customer option
        And I select "equal" as the constraints comparator
        And I enter true as value for the constraint with customer option "Boolean Option"

        And I save my changes

        Then the customer option group "Some Group" should have constraints:
            | option         | comparator | value              | error_message |
            | Text Option    | equal      | 5                  | Oops          |
            | Select Option  | in_set     | "Value-1, Value-3" | Oops          |
            | Boolean Option | equal      | true               | Oops          |

    @ui
    Scenario: Saving multiple validators
        Given I want to edit customer option group "Some Group"

        When I add a validator
        And I define the validators error message as "msg 1"

        And I add a condition
        And I pick "Text Option" as the conditions customer option
        And I select "lesser" as the conditions comparator
        And I enter 10 as value for the condition with customer option "Text Option"

        When I add a validator
        And I define the validators error message as "msg 2"

        And I add a condition
        And I pick "Number Option" as the conditions customer option
        And I select "equal" as the conditions comparator
        And I enter 10 as value for the condition with customer option "Number Option"

        When I add a validator
        And I define the validators error message as "msg 3"

        And I add a constraint
        And I pick "Text Option" as the constraints customer option
        And I select "greater" as the constraints comparator
        And I enter 10 as value for the constraint with customer option "Text Option"

        And I save my changes

        Then the customer option group "Some Group" should have conditions:
            | option         | comparator | value | error_message |
            |  Text Option   |  lesser    | 10    | msg 1         |
            |  Number Option |  equal     | 10    | msg 2         |

        And the customer option group "Some Group" should have constraints:
            | option      | comparator | value | error_message |
            | Text Option | greater    | 10    | msg 3         |
