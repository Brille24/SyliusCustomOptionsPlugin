<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Element\NodeElement;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
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
        if(CustomerOptionTypeEnum::isSelect($customerOption->getType())){
            $this->getDocument()->selectFieldOption(
                $customerOption->getName(), $value,
                $customerOption->getType() === CustomerOptionTypeEnum::MULTI_SELECT
            );
        }else {
            $this->getDocument()->fillField($customerOption->getName(), $value);
        }
    }

    /**
     * @return bool
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasRequiredCustomerOptionValidationMessage(){
        $message = "This option is required.";

        return $this->hasValidationMessage($message);
    }

    /**
     * @return bool
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasInvalidCustomerOptionValidationMessage(){
        $message = "This value is not valid.";

        return $this->hasValidationMessage($message);
    }

    /**
     * @return bool
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasOptionOutOfBoundsValidationMessage(){
        $message = "This value should be";

        if(!$this->hasElement('validation_errors')){
            return false;
        }

        $errors = $this->getElement('validation_errors')->getText();

        return strpos($this->getElement('validation_errors')->getText(), $message) !== false;
    }

    /**
     * @param string $message
     * @return bool
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    private function hasValidationMessage(string $message){
        if(!$this->hasElement('validation_errors')){
            return false;
        }

        return $this->getElement('validation_errors')->getText() === $message;
    }
}