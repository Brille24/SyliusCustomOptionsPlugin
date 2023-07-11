<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage
{
    /**
     * @throws ElementNotFoundException
     */
    public function setName(string $name): void
    {
        $this->getDocument()->fillField('Name', $name);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function chooseType(string $type): void
    {
        $this->getDocument()->selectFieldOption('Type', $type);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setRequired(): void
    {
        $this->getDocument()->checkField('Required');
    }

    public function hasConfiguration(string $config): bool
    {
        $result = $this->getDocument()->hasField($config);

        if (!$result) {
            $fields = $this->getDocument()->findAll('css', '.field');

            /** @var NodeElement $requiredField */
            foreach ($fields as $requiredField) {
                $label = $requiredField->find('css', 'label');

                if ($label !== null && $label->getText() === $config) {
                    $result = $requiredField->has('css', 'div[id^="brille24_customer_option_configuration"]');

                    break;
                }
            }
        }

        return $result;
    }

    public function hasPriceConfiguration($valueName, $channelName): bool
    {
        $pricingTab = $this->getDocument()->find('css', 'div[data-tab="pricing"]');

        $channelTab = $pricingTab->find('css', sprintf('div[data-tab="%s"]', $channelName));

        return $channelTab->has('css', sprintf('input[value="%s"][readonly="readonly"]', $valueName));
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setField(string $field, string $value): void
    {
        $this->getDocument()->fillField($field, $value);
    }

    public function hasLink(string $name): bool
    {
        return $this->getDocument()->hasLink($name);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addValue(string $code, string $name): void
    {
        $this->getDocument()->clickLink('Add');

        $valuesNode = $this->getDocument()->find('css', '#brille24_customer_option_values');

        $valueItems = $valuesNode->findAll('css', 'div[data-form-collection="item"]');

        /** @var NodeElement $lastValueItem */
        $lastValueItem = end($valueItems);

        $lastValueItem->fillField('Code', $code);
        $lastValueItem->fillField('Name', $name);
    }

    protected function getDefinedElements(): array
    {
        return [
            'code' => '#brille24_customer_option_code',
        ];
    }
}
