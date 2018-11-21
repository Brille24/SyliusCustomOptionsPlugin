<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPage as BaseUpdatePage;

class UpdateConfigurableProductPage extends BaseUpdatePage
{
    /**
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function selectCustomerOptionGroup(string $name): void
    {
        $customerOptionsTab = $this->getDocument()->find('css', 'div[data-tab=customer_options]');

        $selectItems = $customerOptionsTab->waitFor(2, function () use ($customerOptionsTab) {
            return $customerOptionsTab->findAll('css', 'select');
        });
        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'select');
        }

        $lastSelectItem->selectOption($name);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addCustomerOptionValuePrice(): void
    {
        $customerOptionsTab = $this->getDocument()->find('css', 'div[data-tab=customer_options]');
        $customerOptionsTab->clickLink('Add');
    }

    /**
     * @param string $valueName
     *
     * @throws ElementNotFoundException
     */
    public function chooseOptionValue(string $valueName): void
    {
        $customerOptionsTab = $this->getDocument()->find('css', 'div[data-tab=customer_options]');

        /** @var NodeElement[] $valuePrices */
        $valuePrices = $customerOptionsTab->findAll('css',
            'div[data-form-collection="item"]'
        );

        $lastValuePrice = end($valuePrices);

        if (false === $lastValuePrice) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', 'div[id^="sylius_product_customer_option_value_prices"]');
        }

        $lastValuePrice->selectFieldOption('Customer option value', $valueName);
    }

    /**
     * @param int $amount
     *
     * @throws ElementNotFoundException
     */
    public function setValuePriceAmount(int $amount): void
    {
        $customerOptionsTab = $this->getDocument()->find('css', 'div[data-tab=customer_options]');

        /** @var NodeElement[] $valuePrices */
        $valuePrices = $customerOptionsTab->findAll('css',
            'div[data-form-collection="item"]'
        );

        $lastValuePrice = end($valuePrices);

        if (false === $lastValuePrice) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', 'div[id^="sylius_product_customer_option_value_prices"]');
        }

        $lastValuePrice->fillField('Amount', $amount);
    }

    /**
     * @param string $type
     *
     * @throws ElementNotFoundException
     */
    public function setValuePriceType(string $type): void
    {
        $customerOptionsTab = $this->getDocument()->find('css', 'div[data-tab=customer_options]');

        /** @var NodeElement[] $valuePrices */
        $valuePrices = $customerOptionsTab->findAll('css',
            'div[data-form-collection="item"]'
        );

        $lastValuePrice = end($valuePrices);

        if (false === $lastValuePrice) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', 'div[id^="sylius_product_customer_option_value_prices"]');
        }

        $lastValuePrice->selectFieldOption('Type', $type);
    }

    public function openCustomerOptionsTab(): void
    {
        $customerOptionsTab = $this->getDocument()->find('css', 'a[data-tab="customer_options"]');

        $customerOptionsTab->click();
    }
}
