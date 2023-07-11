<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;
use Sylius\Bundle\CoreBundle\Application\Kernel as SyliusKernel;

class ShowPage extends BaseShowPage
{
    /**
     * @throws DriverException
     * @throws UnsupportedDriverActionException
     */
    public function setCookie($name, $value): void
    {
        $driver = $this->getSession()->getDriver();

        $driver->setCookie($name, $value);
    }

    public function hasCustomizationFor(CustomerOptionInterface $customerOption): bool
    {
        $result = $this->getDocument()->hasField($customerOption->getName());

        if (!$result) {
            $fields = $this->getDocument()->findAll('css', '.field');

            /** @var NodeElement $field */
            foreach ($fields as $field) {
                $label = $field->find('css', 'label');

                if ($label !== null && $label->getText() === $customerOption->getName()) {
                    $result = $field->has('css', 'div[id^="sylius_add_to_cart_customer_options"]');

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function fillCustomerOption(CustomerOptionInterface $customerOption, string $value): void
    {
        if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
            $this->getDocument()->selectFieldOption(
                $customerOption->getName(),
                $value,
                $customerOption->getType() === CustomerOptionTypeEnum::MULTI_SELECT,
            );
        } elseif (CustomerOptionTypeEnum::isDate($customerOption->getType())) {
            /** @var NodeElement[] $labels */
            $labels = $this->getDocument()->findAll('css', 'div > label');

            $field = null;
            foreach ($labels as $label) {
                if ($label->getText() === $customerOption->getName()) {
                    $field = $label->getParent();

                    break;
                }
            }

            $dateValue = new \DateTime($value);

            $dateFields = $customerOption->getType() === CustomerOptionTypeEnum::DATETIME ?
                $field->find('css', 'div[id$="date"]') :
                $field->find('css', 'div')
            ;

            $day = $dateFields->find('css', 'select[id$="day"]');
            $month = $dateFields->find('css', 'select[id$="month"]');
            $year = $dateFields->find('css', 'select[id$="year"]');

            $day->selectOption($dateValue->format('j'));
            $month->selectOption($dateValue->format('M'));
            $year->setValue($dateValue->format('Y'));

            $timeFields = $field->find('css', 'div[id$="time"]');

            if ($timeFields !== null) {
                $hour = $timeFields->find('css', 'select[id$="hour"]');
                $minute = $timeFields->find('css', 'select[id$="minute"]');

                $hour->selectOption($dateValue->format('H'));
                $minute->selectOption($dateValue->format('i'));
            }
        } elseif ($customerOption->getType() === CustomerOptionTypeEnum::BOOLEAN) {
            $this->checkField($customerOption->getName(), filter_var($value, \FILTER_VALIDATE_BOOLEAN));
        } else {
            $this->getDocument()->fillField($customerOption->getName(), $value);
        }
    }

    /**
     * @throws DriverException
     * @throws UnsupportedDriverActionException
     */
    private function checkField(string $fieldName, bool $state): void
    {
        /** @var NodeElement $field */
        $field = $this->getDocument()->findField($fieldName);

        $script = sprintf('$("#%s").prop("checked", %s);', $field->getAttribute('id'), ($state) ? 'true' : 'false');

        $this->getDriver()->executeScript($script);
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasRequiredCustomerOptionValidationMessage(): bool
    {
        return $this->hasValidationMessage('This option is required');
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasInvalidCustomerOptionValidationMessage(): bool
    {
        return $this->hasValidationMessage('This value is not valid.');
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasInvalidNumberValidationMessage(): bool
    {
        if (version_compare(SyliusKernel::VERSION, '1.12.0', '<')) {
            return $this->hasValidationMessage('This value is not valid.');
        }

        return $this->hasValidationMessage('Please enter a number.');
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function hasOptionOutOfBoundsValidationMessage(): bool
    {
        $message = 'This value';

        if (!$this->hasElement('validation_errors')) {
            return false;
        }

        $errors = $this->getElement('validation_errors')->getText();

        return str_contains($errors, $message);
    }

    /**
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    private function hasValidationMessage(string $message): bool
    {
        if (!$this->hasElement('validation_errors')) {
            return false;
        }

        return $this->getElement('validation_errors')->getText() === $message;
    }

    public function hasValidationErrors(): bool
    {
        return $this->hasElement('validation_errors');
    }
}
