<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Element\NodeElement;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;

class ShowPage extends BaseShowPage
{
    /**
     * @param CustomerOptionInterface $customerOption
     * @return bool
     */
    public function hasCustomizationFor(CustomerOptionInterface $customerOption){

        $result = $this->getDocument()->hasField($customerOption->getName());

        if(!$result){
            $fields = $this->getDocument()->findAll('css', '.field');

            /** @var NodeElement $field */
            foreach ($fields as $field){
                $label = $field->find('css', 'label');

                if($label !== null && $label->getText() === $customerOption->getName()){
                    $result = $field->has('css', 'div[id^="sylius_add_to_cart_customerOptions"]');
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param CustomerOptionInterface $customerOption
     * @param string $value
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillCustomerOption(CustomerOptionInterface $customerOption, string $value){
        $this->getDocument()->fillField($customerOption->getName(), $value);
    }
}