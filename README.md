# Customer Options
<p align="center"><img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="100px"></p>

With this plugin the customer can add additional info to the product like so:
![Price import forms](docs/images/customeroption_frontend.png "The customer can upload a file")
![Price import forms](docs/images/customeroption_frontend_cart.png "And it will be displayed in the cart")

## Installation

* Run `composer require brille24/sylius-customer-options-plugin`.

* Register the Plugin in your `config/bundles.php`:

```php
return [
    //...
    Brille24\SyliusCustomerOptionsPlugin\Brille24SyliusCustomerOptionsPlugin::class => ['all' => true],
];
```
* Add the `config.yml` to your local `config/packages/_sylius.yaml`:
```yaml
imports:
    ...
    - { resource: "@Brille24SyliusCustomerOptionsPlugin/Resources/config/app/config.yml" }
```

* Add the `routing.yml` to your local `config/routes.yaml`:
```yaml
brille24_customer_options:
    resource: "@Brille24SyliusCustomerOptionsPlugin/Resources/config/app/routing.yml"

sylius_shop_ajax_cart_add_item:
  path: ajax/cart/add
  methods: [POST]
  defaults:
    _controller: sylius.controller.order_item::addAction
    _format: json
    _sylius:
      factory:
        method: createForProductWithCustomerOption
        arguments: [expr:notFoundOnNull(service('sylius.repository.product').find($productId))]
      form:
        type: Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType
        options:
          product: expr:notFoundOnNull(service('sylius.repository.product').find($productId))
      redirect:
        route: sylius_shop_cart_summary
        parameters: {}
      flash: sylius.cart.add_item

sylius_shop_partial_cart_add_item:
  path: cart/add-item
  methods: [GET]
  defaults:
    _controller: sylius.controller.order_item::addAction
    _sylius:
      template: $template
      factory:
        method: createForProductWithCustomerOption
        arguments: [expr:notFoundOnNull(service('sylius.repository.product').find($productId))]
      form:
        type: Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType
        options:
          product: expr:notFoundOnNull(service('sylius.repository.product').find($productId))
      redirect:
        route: sylius_shop_cart_summary
        parameters: {}
```

* Copy the template overrides from the plugin directory
```
From: [shop_dir]/vendor/brille24/sylius-customer-options-plugin/test/Application/templates
To: [shop_dir]/templates
```

In order to use the customer options, you need to override the product and order item.
```php
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Traits\ProductCustomerOptionCapableTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ProductInterface {
    use ProductCustomerOptionCapableTrait {
        __construct as protected customerOptionCapableConstructor;
    }
    
     public function __construct()
    {
        parent::__construct();

        $this->customerOptionCapableConstructor();
    }
    // ...
}
```

```php
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Traits\OrderItemCustomerOptionCapableTrait;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    use OrderItemCustomerOptionCapableTrait {
        __construct as protected customerOptionCapableConstructor;
    }

    public function __construct()
    {
        parent::__construct();

        $this->customerOptionCapableConstructor();
    }
    // ...
}
```

* If you also want default data you need to copy over the `brille24_sylius_customer_options_plugin_fixtures.yaml` file from the package directory and run
```bash
bin/console sylius:fixtures:load
```

* Finally, generate migrations, update the database and update the translations:
```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
bin/console translation:update
```

## Things to consider
* Saving files as customer defined values as the values are currently stored as a string in the database

## Developing
When developing it is recommended to use git hooks for this just copy the `docs/pre-commit` to `.git/hooks/pre-commit` and make it executable. Then you will check your codestyle before committing.

## Usage
Documentation on how to use the plugin can be found [here](docs/usage.md).
