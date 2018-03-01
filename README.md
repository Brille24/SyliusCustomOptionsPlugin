<h1 align="center">Customer Options</h1>
This bundle provides the customer to ability to customize the product to his/her liking. This is a different from Sylius internal product variant structure, as it is more flexible and allows to use them without pre-generating a lot of data.

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
in case 1 you will have the option to add values for the customer to choose from (which then can be priced in the pricing tab). 
In case 2 you can set some validation options for the entered value. Those are however not priced.

After you have created all the Customer Options, you can arrange them in Customer Option Groups where every Customer Option gets ordered
in a list with distinct positions. This Customer Option Group can now be assigned to the product. 

### Generating Customer Options with fixtures
TODO!

## Things to consider
* This plugin does not take the [tier price plugin](https://packagist.org/packages/brille24/tierprice-plugin) into account. The tier prices will override the pricing model that is implemented here (if you use the default implementation that comes with the plugin)
