# Customer Options
<p align="center"><img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="100px"></p>
<a href="https://travis-ci.org/Brille24/SyliusCustomOptionsPlugin">
	<img src="https://travis-ci.org/Brille24/SyliusCustomOptionsPlugin.svg?branch=master" />
</a>

## Installation

* Run `composer require brille24/sylius-customer-options-plugin`.

* Register the Plugin in your `AppKernel` file:

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...

        new \Brille24\SyliusCustomerOptionsPlugin\Brille24SyliusCustomerOptionsPlugin(),
    ]);
}
```
* Add the `config.yml` to your local `app/config/config.yml`:
```yaml
imports:
    ...
    - { resource: "@Brille24SyliusCustomerOptionsPlugin/Resources/config/app/config.yml" }
```

* Add the `routing.yml` to your local `app/config/routing.yml`:
```yaml
brille24_customer_options:
    resource: "@Brille24SyliusCustomerOptionsPlugin/Resources/config/app/routing.yml"
```

* Copy the template overrides from the plugin directory
```
From: [shop_dir]/vendor/brille24/customer-options/test/Application/app/Resources
To: [shop_dir]/app/Resources
```

* Finally update the database and update the translations:
```bash
bin/console doctrine:schema:update --force
bin/console translation:update
```

## Usage
There are two cases that we want to describe. In the first case of "Making a product customizable" we go through the process
of creating everything from scratch. In the "Generating Customer Options with fixtures" we use fixtures to generate 
random Customer Options.
**To see images of how it looks look into the `images` folder.**

### Making a product customizable
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

### Conditional constraints
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

### Generating Customer Options with fixtures
Add the following to your `app/config/fixtures.yml`:
```yaml
sylius_fixtures:
    suites:
        default: #Can be any suite you want
            fixtures:
                brille24_customer_option: #For Customer Options
                
                brille24_customer_option_group: #For Customer Option Groups
```

There are two possible ways to add Customer Options to your fixture.
1. Generate them randomly:
```yaml
...
    fixtures:
        brille24_customer_option:
            options:
                amount: 10
        
        brille24_customer_option_group:
            options:
                amount: 2
```
This will generate 10 Customer Options and 2 Customer Option Groups. Generated Customer Options will automatically assign
themselves to at least one Group, and generated Customer Option Groups will assign at least one Option and Product to
themselves (of course only, if there are any defined). But they won't create conditional constraints.

2. Define them yourself:
```yaml
...
    fixtures:
        brille24_customer_option:
            options:
                custom:
                -   code: some_option #Required
                    
                    translations: #Required
                      en_US: Some Option #Every option needs at least one translation.
                      
                    type: select #Can be any of the defined option types. Defaults to 'text'.
                    
                    values: #Value definitions only do something, if the type is 'select' or 'multi_select'.
                    -   code: some_value #Required
                      
                        translations: #Required
                            en_US: Some Value #Also needs at least one translation.
                        
                        prices: #Undefined prices are generated automatically for every option value and channel with default values.
                            
                        -   type: fixed #Can be 'fixed' or 'percent'. Defaults to 'fixed'.
                            
                            amount: 100 #The fixed amount in cents. Defaults to 0.
                            
                            percent: 0 #The percentage of the base price. Defaults to 0.
                            
                            channel: US_WEB #The channel code. Defaults to 'default'.
                              
                    configuration: #Configuration definitions can only be used with non select options
                        brille24.form.config.max.length: 100 #For a list of available options refer to 'src/Enumerations/CustomerOptionTypeEnum.php:118'
                              
                    required: false #Defaults to false.
                    
                    groups: ~ #Put codes of defined groups here.
                      
        brille24_customer_option_group:
            options:
                custom:
                -   code: some_group #Required
                      
                    translations: #Required
                        en_US: Some Group #Needs at least one translation.
                          
                    options: #Put codes of defined options here.
                          - some_option
                          
                    validators:
                    -   conditions: #Not required
                        -   customer_option: some_option #Required
                            comparator: in_set #Required, must be one of the defined comparators in Enumerations/ConditionComparatorEnum.php
                            value: val_1 #Required, for select options put the value codes seperated with commas here
                          
                        constraints: #Same as conditions
                        -   customer_option: some_option
                            comparator: not_in_set
                            value: val_2
                                  
                        error_messages: #Not required
                            en_US: Oops! #At least one required
                          
                    products: ~ #Put codes of defined products here.
```

You can also assign a group and override value prices in the product fixture.
```yaml
...
    fixtures:
        product:
            custom:
            - ... #The usual product definitions.
                
                customer_option_group: some_group #The code of the customer option group the product should have assigned.
                  
                customer_option_value_prices: #Override the prices for customer option values per channel.
                      
                -   value_code: some_value #Required. Values that are not present in the assigned group are ignored.
                       
                    type: percent #Defaults to 'fixed'.
                       
                    amount: 0 #Defaults to 0.
                       
                    percent: 10 #Defaults to 0.
                      
                    channel: US_WEB #Defaults to 'default'.
```

When you finished defining all your fixtures, run `bin/console sylius:fixtures:load` to load your fixtures.

## Things to consider
* Just like the tier price plugin, this plugin overrides the update cart functionality if you want to implement an event bases solution, you need to comment out the `Brille24\SyliusCustomerOptionsPlugin\Services\OrderPricesRecalculator` in the `services.xml` in the plugin's resource folder.
* Saving files as customer defined values as the values are currently stored as a string in the database
