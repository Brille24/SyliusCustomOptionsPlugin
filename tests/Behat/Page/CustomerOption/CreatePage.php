<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

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
            $fields = $this->getDocument()->findAll('css', '.field');

            /** @var NodeElement $requiredField */
            foreach ($fields as $requiredField){
                $label = $requiredField->find('css', 'label');

                if($label !== null && $label->getText() === $config){
                    $result = $requiredField->has('css', 'div[id^="brille24_customer_option_configuration"]');
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param $valueName
     * @param $channelName
     * @return bool
     */
    public function hasPriceConfiguration($valueName, $channelName){
        $pricingTab = $this->getDocument()->find('css', 'div[data-tab="pricing"]');

        $channelTab = $pricingTab->find('css', sprintf('div[data-tab="%s"]', $channelName));

        return $channelTab->has('css', sprintf('input[value="%s"][readonly="readonly"]', $valueName));
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

        $valueItems = $valuesNode->findAll('css', 'div[data-form-collection="item"]');

        /** @var NodeElement $lastValueItem */
        $lastValueItem = end($valueItems);

        $lastValueItem->fillField('Code', $code);
        $lastValueItem->fillField('Name', $name);
    }

    protected function getDefinedElements()
    {
        return [
            'code' => '#brille24_customer_option_code',
        ];
    }
}