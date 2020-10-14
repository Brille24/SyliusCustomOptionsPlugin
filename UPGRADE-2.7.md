# Upgrade from 2.x to 2.7
Sylius switched to the `3.0` version of Doctrine Migrations.

1. Remove our migrations from your project:
`rm src/Migrations/Version20191010092727.php src/Migrations/Version20191010092728.php`

2. We were missing migrations that now got generated.
If you already diffed them yourself, mark it as executed with:
 `bin/console doctrine:migrations:version "Brille24\SyliusCustomerOptionsPlugin\Migrations\Version20191010092726" --add --no-interaction`.
