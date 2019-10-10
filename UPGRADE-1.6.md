# UPGRADE FROM `v1.4.X` TO `v1.6.x`

Require upgraded Plugin version using Composer:

```bash
composer require brille24/sylius-customer-options-plugin:~1.6.0
```

Copy [a new migration file](https://raw.githubusercontent.com/Brille24/SyliusCustomerOptionsPlugin/master/tests/Application/src/Migrations/Version20191010092727.php) and run new migrations:

```bash
bin/console doctrine:migrations:migrate
```

To add the CustomerOption type to your existing OrderItemOptions you can run following command:
```bash
bin/console b24:customer-options:update-order-item-options-type
```
