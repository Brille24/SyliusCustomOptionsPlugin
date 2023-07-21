<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Checkout\CheckoutPage;
use Webmozart\Assert\Assert;

class CheckoutContext implements Context
{
    public function __construct(private CheckoutPage $checkoutPage)
    {
    }

    /**
     * @Then I should have a configuration price of :additionalPrice
     */
    public function iShouldHaveConfigurationPriceOf($additionalPrice)
    {
        Assert::true($this->checkoutPage->hasCustomerOptionAdditionalPrice($additionalPrice));
    }
}
