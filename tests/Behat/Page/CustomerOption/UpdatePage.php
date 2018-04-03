<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage
{
    /**
     * @param string $name
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setName(string $name)
    {
        $this->getDocument()->fillField('Name', $name);
    }

    /**
     * @param string $code
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setCode(string $code)
    {
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @param string $type
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function chooseType(string $type)
    {
        $this->getDocument()->selectFieldOption('Type', $type);
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setRequired()
    {
        $this->getDocument()->checkField('Required');
    }

    /**
     * @param string $config
     *
     * @return bool
     */
    public function hasConfiguration(string $config)
    {
        $result = $this->getDocument()->hasField($config);

        if (!$result) {
            $requiredFields = $this->getDocument()->findAll('css', '.field');

            /** @var NodeElement $requiredField */
            foreach ($requiredFields as $requiredField) {
                $label = $requiredField->find('css', 'label');

                if ($label !== null && $label->getText() === $config) {
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
     *
     * @return bool
     */
    public function hasPriceConfiguration($valueName, $channelName)
    {
        $pricingTab = $this->getDocument()->find('css', 'div[data-tab="pricing"]');

        $channelTab = $pricingTab->find('css', sprintf('div[data-tab="%s"]', $channelName));

        return $channelTab->has('css', sprintf('input[value="%s"][readonly="readonly"]', $valueName));
    }

    protected function getDefinedElements()
    {
        return [
            'code' => '#brille24_customer_option_code',
        ];
    }
}
