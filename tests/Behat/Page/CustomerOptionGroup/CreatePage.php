<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Behat\Page\CustomerOptionGroup;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CreatePage extends BaseCreatePage
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * CreatePage constructor.
     *
     * @param Session $session
     * @param array|\ArrayAccess $minkParameters
     * @param RouterInterface $router
     * @param string $routeName
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        string $routeName,
        TranslatorInterface $translator
    ) {
        parent::__construct($session, $minkParameters, $router, $routeName);

        $this->translator = $translator;
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillField(string $field, string $value): void
    {
        $this->getDocument()->fillField($field, $value);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addOption(): void
    {
        $this->getDocument()->clickLink($this->translator->trans('brille24.form.customer_option_groups.buttons.add_option'));
    }

    /**
     * @param string $name
     * @param int $position
     *
     * @throws ElementNotFoundException
     */
    public function chooseOption(string $name, int $position): void
    {
        $selectItems = $this->getDocument()->waitFor(2, function () {
            return $this->getDocument()->findAll('css', 'div[data-form-type="collection"][id$="option_associations"] select');
        });
        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"][id$="option_associations"] select');
        }

        /** @var NodeElement[] $numberItems */
        $numberItems = $this->getDocument()->findAll('css', 'div[data-form-type="collection"][id$="option_associations"] input[type="number"]');
        $lastNumberItem = end($numberItems);

        if (false === $lastNumberItem) {
            throw new ElementNotFoundException($this->getSession(), 'input', 'css', 'div[data-form-type="collection"][id$="option_associations"] input[type="number"]');
        }

        $lastSelectItem->selectOption($name);
        $lastNumberItem->setValue($position);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addValidator(): void
    {
        $this->getDocument()->clickLink($this->translator->trans('brille24.form.customer_option_groups.buttons.add_validator'));
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addCondition(): void
    {
        $this->getDocument()->clickLink($this->translator->trans('brille24.form.validators.buttons.add_condition'));
    }

    /**
     * @throws ElementNotFoundException
     */
    public function addConstraint(): void
    {
        $this->getDocument()->clickLink($this->translator->trans('brille24.form.validators.buttons.add_constraint'));
    }

    /**
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function chooseOptionForCondition(string $name): void
    {
        /** @var NodeElement[] $conditionDiv */
        $conditionDiv = $this->getDocument()->findAll('css', 'div[data-form-type="collection"][id$="conditions"]');
        $lastConditionDiv = end($conditionDiv);

        $selectItems = $lastConditionDiv->findAll('named', [
            'field',
            $this->translator->trans('brille24.form.validators.fields.customer_option'),
        ]);

        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"][id$="conditions"] select');
        }

        $lastSelectItem->selectOption($name);
    }

    /**
     * @param string $name
     *
     * @throws ElementNotFoundException
     */
    public function chooseComparatorForCondition(string $name): void
    {
        /** @var NodeElement[] $conditionDiv */
        $conditionDiv = $this->getDocument()->findAll('css', 'div[data-form-type="collection"][id$="conditions"]');
        $lastConditionDiv = end($conditionDiv);

        $selectItems = $lastConditionDiv->findAll('named', [
            'field',
            $this->translator->trans('brille24.form.validators.fields.comparator'),
        ]);

        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"][id$="conditions"] select');
        }

        $lastSelectItem->selectOption($name);
    }

    /**
     * @param string $optionType
     *
     * @return null|string
     */
    public function getConditionValueType(string $optionType): ?string
    {
        $valueSuffix = '.default';
        if ($optionType === CustomerOptionTypeEnum::TEXT) {
            $valueSuffix = '.text';
        } elseif (CustomerOptionTypeEnum::isSelect($optionType)) {
            $valueSuffix = '.set';
        }

        /** @var NodeElement[] $conditionDiv */
        $conditionDiv = $this->getDocument()->findAll('css', 'div[data-form-type="collection"][id$="conditions"]');
        $lastConditionDiv = end($conditionDiv);

        /** @var NodeElement[] $valueItems */
        $valueItems = $lastConditionDiv->findAll('named', [
            'field',
            $this->translator->trans('brille24.form.validators.fields.value' . $valueSuffix),
        ]);

        $lastValueItem = end($valueItems);

        return $lastValueItem->getAttribute('type') ?? $lastValueItem->getTagName();
    }

    /**
     * @return int
     */
    public function countValidators(): int
    {
        $validators = $this->getDocument()->findAll('css', 'div[data-form-type="collection"][id$="validators"] > div > div');

        return count($validators);
    }
}
