## Making a product customizable
In order to add a Customer Option to a product you first have to create the Customer Option. There are two types of CustomerOptions:

1. Customer Options with predefined values: Those basically work similar to the "Options" concept that Sylius implements however you
don't have to generate all the possible configurations beforehand.
1. Customer Options with a free user based entry: This is where the Customer Options are the most valuable. You can offer the
customer to enter text that will be displayed in the order. Currently those Customer Options can not be priced.

The workflow is that you provide a code and a type and save the Customer Option. Then the appropriate fields will be shown.
In case 1 you will have the option to add values for the customer to choose from (which then can be priced in the pricing tab). 
In case 2 you can set some validation options for the entered value. **Those are however not priced.**

After you have created all the Customer Options, you can arrange them in Customer Option Groups where every Customer Option gets ordered
in a list with distinct positions. This Customer Option Group can now be assigned to the product. 
