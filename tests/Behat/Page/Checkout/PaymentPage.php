<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Checkout;

class PaymentPage extends \Sylius\Behat\Page\Shop\Checkout\SelectPaymentPage
{
    use NextStepTrait;
}
