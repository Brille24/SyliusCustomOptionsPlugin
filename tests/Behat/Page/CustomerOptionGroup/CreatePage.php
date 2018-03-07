<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOptionGroup;

use Behat\Mink\Element\NodeElement;
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
     * @param int $position
     * @throws ElementNotFoundException
     */
    public function chooseOption(string $name, int $position){
        $selectItems = $this->getDocument()->waitFor(2, function () {
            return $this->getDocument()->findAll('css', 'div[data-form-type="collection"] select');
        });
        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"] select');
        }

        /** @var NodeElement[] $numberItems */
        $numberItems = $this->getDocument()->findAll('css', 'div[data-form-type="collection"] input[type="number"]');
        $lastNumberItem = end($numberItems);

        if (false === $lastNumberItem){
            throw new ElementNotFoundException($this->getSession(), 'input', 'css', 'div[data-form-type="collection"] input[type="number"]');
        }

        $lastSelectItem->selectOption($name);
        $lastNumberItem->setValue($position);
    }
}