## Generating Customer Options with fixtures
If you want to use fixtures, add the following to your `config/fixtures.yaml`:
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
