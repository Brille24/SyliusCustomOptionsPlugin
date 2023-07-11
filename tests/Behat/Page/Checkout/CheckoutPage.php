<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Checkout;

use Sylius\Behat\Page\Shop\Checkout\CompletePage;

class CheckoutPage extends CompletePage
{
    public function hasCustomerOptionAdditionalPrice($price): bool
    {
        return false !== strpos($this->getElement('customer_option_total')->getText(), $price);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_option_total' => '#customer-option-total',
        ]);
    }
}
