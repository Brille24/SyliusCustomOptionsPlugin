<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOptionGroup;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage
{
    /**
     * @param string $field
     * @param string $value
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillField(string $field, string $value){
        $this->getDocument()->fillField($field, $value);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addOption(){
        $this->getDocument()->clickLink('Add');
    }

    /**
     * @param string $name
     * @throws ElementNotFoundException
     */
    public function chooseOption(string $name){
        $selectItems = $this->getDocument()->waitFor(2, function () {
            return $this->getDocument()->findAll('css', 'div[data-form-type="collection"] select');
        });
        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"] select');
        }

        $lastSelectItem->selectOption($name);
    }
}