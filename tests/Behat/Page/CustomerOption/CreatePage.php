<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Symfony\Component\Routing\RouterInterface;

class CreatePage extends BaseCreatePage
{
    /**
     * @param string $name
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setName(string $name){
        $this->getDocument()->fillField('Name', $name);
    }

    /**
     * @param string $code
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setCode(string $code){
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @param string $type
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function chooseType(string $type){
        $this->getDocument()->selectFieldOption('Type', $type);
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setRequired(){
        $this->getDocument()->checkField('Required');
    }

    /**
     * @param string $config
     * @return bool
     */
    public function hasConfiguration(string $config)
    {
        $result = $this->getDocument()->hasField($config);

        if(!$result){
            // Look for group of fields
        }

        return $result;
    }

    /**
     * @param $field
     * @param $value
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setField(string $field, string $value){
        $this->getDocument()->fillField($field, $value);
    }

    public function hasLink(string $name){
        return $this->getDocument()->hasLink($name);
    }

    /**
     * @param string $code
     * @param string $name
     * @throws ElementNotFoundException
     */
    public function addValue(string $code, string $name){
        $this->getDocument()->clickLink('Add');

        $valuesNode = $this->getDocument()->find('css', '#brille24_customer_option_values');

        $valueItems = $valuesNode->findAll('css', 'div');

        /** @var NodeElement $lastValueItem */
        $lastValueItem = end($valueItems);

        $lastValueItem->fillField('Code', $code);
        $lastValueItem->fillField('Name', $name);
    }
}