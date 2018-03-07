<h1 align="center">Customer Options</h1>
This bundle gives the customer the ability to customize the product to his/her liking. This is a different from Sylius internal product variant structure, as it is more flexible and allows to use them without pre-generating a lot of data.

## Installation

* Run `composer require brille24/customer-options`.

* Register the Plugin in your `AppKernel` file:

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...

        new \Brille24\CustomerOptionsPlugin\Brille24CustomerOptionsPlugin(),
    ]);
}
```
* Add the `config.yml` to your local `app/config/config.yml`:
```yaml
imports:
    ...
    - { resource: "@Brille24CustomerOptionsPlugin/Resources/config/app/config.yml" }
```

* Add the `routing.yml` to your local `app/config/routing.yml`:
```yaml
brille24_customer_options:
    resource: "@Brille24CustomerOptionsPlugin/Resources/config/app/routing.yml"
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
In order to add a Customer Option to a product you first have to create the Customer Options. There are two types of CustomerOptions:

1. Customer Options with predefined values: Those basically work similar to the "Options" concept that Sylius implements however you
don't have to generate all the possible configurations beforehand.
1. Customer Options with a free user based entry: This is where the Customer Options are the most valuable. You can offer the
customer to enter text that will be displayed in the order. Currently those Customer Options can not be priced.

The workflow of is that you provide a code and a type and save the Customer Option. Then the appropriate fields will be shown.
In case 1 you will have the option to add values for the customer to choose from (which then can be priced in the pricing tab). 
In case 2 you can set some validation options for the entered value. Those are however not priced.

After you have created all the Customer Options, you can arrange them in Customer Option Groups where every Customer Option gets ordered
in a list with distinct positions. This Customer Option Group can now be assigned to the product. 

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

There two possible ways to add Customer Options to your fixture.
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
themselves (of course only, if there are any defined).

2. Define them yourself:
```yaml
...
    fixtures:
        brille24_customer_option:
            options:
                custom:
                    - code: some_option #Required
                    
                      translations: #Required
                          en_US: Some Option #Every option needs at least one translation.
                          
                      type: select #Can be any of the defined option types. Defaults to 'text'.
                      
                      values: #Value definitions only do something, if the type is 'select' or 'multi_select'.
                          - code: some_value #Required
                          
                            translations: #Required
                                en_US: Some Value #Also needs at least one translation.
                            
                            prices: #Undefined prices are generated automatically for every option value and channel with default values.
                                
                                - type: fixed #Can be 'fixed' or 'percent'. Defaults to 'fixed'.
                                
                                  amount: 100 #The fixed amount in cents. Defaults to 0.
                                
                                  percent: 0 #The percentage of the base price. Defaults to 0.
                                
                                  channel: US_WEB #The channel code. Defaults to 'default'.
                                  
                      required: false #Defaults to false.
                      
                      groups: ~ #Put codes of defined groups here.
                      
        brille24_customer_option_group:
            options:
                custom:
                    - code: some_group #Required
                      
                      translations: #Required
                          en_US: Some Group #Needs at least one translation.
                          
                      options: #Put codes of defined options here.
                          - some_option
                          
                      products: ~ #Put codes of defined products here.
```

You can also assign a group and override value prices in the product fixture.
```yaml
...
    fixtures:
        product:
            custom:
                - ... #The usual product definitions.
                
                  customer_option_grop: some_group #The code of the customer option group the product should have assigned.
                  
                  customer_option_vaule_prices: #Override the prices for customer option values per channel.
                      
                      - value_code: some_value #Required. Values that are not present in the assigned group are ignored.
                       
                        type: percent #Defaults to 'fixed'.
                       
                        amount: 0 #Defaults to 0.
                       
                        percent: 10 #Defaults to 0.
                      
                        channel: US_WEB #Defaults to 'default'.
```

When you finished defining all your fixtures, run `bin/console sylius:fixtures:load` to load your fixtures.

## Things to consider
* This plugin does not take the [tier price plugin](https://packagist.org/packages/brille24/tierprice-plugin) into account. The tier prices will override the pricing model that is implemented here (if you use the default implementation that comes with the plugin)
* Just like the tier price plugin, this plugin overrides the update cart functionality if you want to implement an event bases solution, you need to comment out the `Brille24\CustomerOptionsPlugin\Services\OrderPricesRecalculator` in the `serices.xml` in the plugin's resource folder.