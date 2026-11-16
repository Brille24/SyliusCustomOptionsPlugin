<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;

trait CustomerOptionsFormTrait
{
    public function openCustomerOptionsTab(): void
    {
        $tab = $this->getDocument()->find('css', '[data-test-side-navigation-tab="customer-options"]');

        if (!$tab instanceof NodeElement) {
            throw new ElementNotFoundException($this->getSession(), 'button', 'css', '[data-test-side-navigation-tab="customer-options"]');
        }

        $tab->click();
    }

    /**
     * @throws ElementNotFoundException
     */
    public function selectCustomerOptionGroup(string $name): void
    {
        $this->customerOptionsTab()->selectFieldOption('Customer Option Group', $name);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addCustomerOptionValuePrice(): void
    {
        $tab = $this->customerOptionsTab();
        $before = count($tab->findAll('css', '[data-test-customer-option-value-price]'));

        $tab->pressButton('Add');

        $this->getDocument()->waitFor(
            5,
            fn () => count($this->customerOptionsTab()->findAll('css', '[data-test-customer-option-value-price]')) > $before,
        );
    }

    /**
     * @throws ElementNotFoundException
     */
    public function chooseOptionValue(string $valueName): void
    {
        $this->lastValuePrice()->selectFieldOption('Customer option value', $valueName);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setValuePriceAmount(int $amount): void
    {
        $this->lastValuePrice()->fillField('Amount', (string) $amount);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setValuePriceType(string $type): void
    {
        $this->lastValuePrice()->selectFieldOption('Type', $type);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function customerOptionsTab(): NodeElement
    {
        $tab = $this->getDocument()->find('css', '#product-customer-options');

        if (!$tab instanceof NodeElement) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', '#product-customer-options');
        }

        return $tab;
    }

    /**
     * @throws ElementNotFoundException
     */
    private function lastValuePrice(): NodeElement
    {
        $valuePrices = $this->customerOptionsTab()->findAll('css', '[data-test-customer-option-value-price]');
        $lastValuePrice = end($valuePrices);

        if (!$lastValuePrice instanceof NodeElement) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', '[data-test-customer-option-value-price]');
        }

        return $lastValuePrice;
    }
}
