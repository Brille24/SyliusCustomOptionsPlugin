<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage
{
    /**
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function setName(string $name): void
    {
        $this->getDocument()->fillField('Name', $name);
    }

    /**
     * @param string $code
     *
     * @throws ElementNotFoundException
     */
    public function setCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @param string $type
     *
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

    /**
     * @param string $config
     *
     * @return bool
     */
    public function hasConfiguration(string $config): bool
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
    public function hasPriceConfiguration($valueName, $channelName): bool
    {
        $pricingTab = $this->getDocument()->find('css', 'div[data-tab="pricing"]');

        $channelTab = $pricingTab->find('css', sprintf('div[data-tab="%s"]', $channelName));

        return $channelTab->has('css', sprintf('input[value="%s"][readonly="readonly"]', $valueName));
    }

    /**
     * @return array
     */
    protected function getDefinedElements(): array
    {
        return [
            'code' => '#brille24_customer_option_code',
        ];
    }
}
