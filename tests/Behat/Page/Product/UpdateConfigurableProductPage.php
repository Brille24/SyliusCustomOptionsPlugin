<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPage as BaseUpdatePage;

class UpdateConfigurableProductPage extends BaseUpdatePage
{
    /**
     * @param string $name
     * @throws ElementNotFoundException
     */
    public function selectCustomerOptionGroup(string $name){
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
    public function addCustomerOptionValuePrice(){
        $customerOptionsTab = $this->getDocument()->find('css', 'div[data-tab=customer_options]');
        $customerOptionsTab->clickLink('Add');
    }
}