## Conditional constraints
Inside a Customer Option Group you have the option to apply constraints dependent on the value of other Customer Options.
To do so, simply click the add validator button on the edit page, then you will see buttons to add conditions and constraints.
When adding a condition or constraint, you have to select the customer option, comparator and value.
The comparators and values are based on the customer option type:
- Select options can be tested whether the selection is (not) in a specified set
- Boolean options can be tested whether they are true or false
- The rest can be tested whether their value/text length is greater, equal or less than a specified value

*The appropriate comparators and values for the selected customer option will be shown after saving.*

You also can edit the error message for each validator to give the customer a clue why the product could not be added to the cart. 

The validation works as follows:
1. The customer tries to add a product with a conditional constraint to the cart
2. For every validator defined in the customer option group
    1. Every condition gets validated
    2. If **all** conditions are met, the constraints get validated
    3. If one or more conditions are not met, the options are treated as valid
3. If all validators say the customer options are valid, the product is added to the cart
