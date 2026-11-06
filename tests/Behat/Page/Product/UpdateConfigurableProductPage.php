<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product;

use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPage as BaseUpdatePage;

class UpdateConfigurableProductPage extends BaseUpdatePage
{
    use CustomerOptionsFormTrait;
}
