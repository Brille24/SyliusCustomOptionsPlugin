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
* Create a customer option group in the admin panel
* Create customer option
* Assign a customer option to the customer option group
* In the product, assign it a customer option

Now you should see in the frontend that upon opening the product you will see that there are customer options to be configured when adding the product to the cart.