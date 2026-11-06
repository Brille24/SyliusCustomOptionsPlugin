<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;

trait CustomerOptionValuesFormTrait
{
    /**
     * @throws ElementNotFoundException
     */
    public function addValue(string $code, string $name): void
    {
        $valuesNode = $this->valuesCollection();
        $before = count($valuesNode->findAll('css', '[data-test-customer-option-value]'));

        $valuesNode->pressButton('Add value');

        $appeared = $this->getDocument()->waitFor(
            5,
            fn () => count($this->valuesCollection()->findAll('css', '[data-test-customer-option-value]')) > $before,
        );
        if (!$appeared) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', '[data-test-customer-option-value]');
        }

        $lastValueItem = $this->lastValue();
        $lastValueItem->fillField('Code', $code);
        $lastValueItem->fillField('English (United States)', $name);
        $this->waitForFormUpdate();
    }

    /**
     * @throws ElementNotFoundException
     */
    private function valuesCollection(): NodeElement
    {
        $valuesNode = $this->getDocument()->find('css', '#brille24_customer_option_values');
        if (!$valuesNode instanceof NodeElement) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', '#brille24_customer_option_values');
        }

        return $valuesNode;
    }

    /**
     * @throws ElementNotFoundException
     */
    private function lastValue(): NodeElement
    {
        $valueItems = $this->valuesCollection()->findAll('css', '[data-test-customer-option-value]');
        $lastValueItem = end($valueItems);
        if (!$lastValueItem instanceof NodeElement) {
            throw new ElementNotFoundException($this->getSession(), 'div', 'css', '[data-test-customer-option-value]');
        }

        return $lastValueItem;
    }
}
