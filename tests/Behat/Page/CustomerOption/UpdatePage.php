<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Behat\Service\DriverHelper;

class UpdatePage extends BaseUpdatePage
{
    use CustomerOptionValuesFormTrait;

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

    public function hasPriceConfigurationForValue(string $valueCode, string $channelName): bool
    {
        $pricingModal = $this->getDocument()->find('css', sprintf('.pricing-modal[data-customer-option-value="%s"]', $valueCode));

        return null !== $pricingModal && $pricingModal->has('css', sprintf('div[data-tab="%s"]', $channelName));
    }

    public function saveChanges(): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->blur();
            $this->waitForFormUpdate();
        }

        $this->getDocument()->find('css', '[data-test-update-changes-button]')->click();
        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function waitForFormUpdate():void
    {
        parent::waitForFormUpdate();
    }

    protected function getDefinedElements(): array
    {
        return [
            'code' => '#brille24_customer_option_code',
        ];
    }
}
