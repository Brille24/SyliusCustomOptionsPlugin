<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Checkout;

use Behat\Mink\Driver\Selenium2Driver;

trait NextStepTrait
{
    public function nextStep(): void
    {
        parent::nextStep();

        // Wait for page load
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getDriver()->wait(10000, "document.readyState === 'complete'");
        }
    }
}
