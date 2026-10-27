<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Condition;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConditionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConstraintInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CustomerOptionGroupInterface $customerOptionGroup */
        $customerOptionGroup = $options['customerOptionGroup'];
        $customerOptions = array_values(array_filter($customerOptionGroup->getOptions()));

        $builder
            ->add('customer_option', ChoiceType::class, [
                'label' => 'brille24.form.validators.fields.customer_option',
                'choices' => $customerOptions,
                'choice_label' => 'name',
                'choice_value' => 'code',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($customerOptions): void {
            $this->addComparatorAndValueFields(
                $event->getForm(),
                $this->getRenderedCustomerOption($event->getData(), $customerOptions),
            );
        });

        // The comparator and value fields depend on the selected customer option, so they are rebuilt when it changes
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($customerOptions): void {
            $data = $event->getData();
            if (!is_array($data)) {
                return;
            }

            $form = $event->getForm();
            $renderedCustomerOption = $this->getRenderedCustomerOption($form->getData(), $customerOptions);
            $submittedCustomerOption = $this->findCustomerOption($customerOptions, $data['customer_option'] ?? null)
                ?? $renderedCustomerOption;

            if ($submittedCustomerOption === $renderedCustomerOption) {
                return;
            }

            $this->addComparatorAndValueFields($form, $submittedCustomerOption, true);

            $optionType = $submittedCustomerOption?->getType() ?? CustomerOptionTypeEnum::TEXT;
            if (!in_array($data['comparator'] ?? null, $this->getComparators($submittedCustomerOption), true)) {
                unset($data['comparator']);
            }
            if (!$this->fitsValueField($data['value']['value'] ?? null, $optionType)) {
                unset($data['value']);
            }

            $event->setData($data);
        });
    }

    /**
     * Returns the customer option the comparator and value fields are rendered for. Without a valid
     * selection, the select shows the first customer option, so the fields are built for that one.
     *
     * @param CustomerOptionInterface[] $customerOptions
     */
    private function getRenderedCustomerOption(mixed $condition, array $customerOptions): ?CustomerOptionInterface
    {
        $customerOption = null;
        if ($condition instanceof ConditionInterface || $condition instanceof ConstraintInterface) {
            $customerOption = $condition->getCustomerOption();
        }

        if ($customerOption !== null && in_array($customerOption, $customerOptions, true)) {
            return $customerOption;
        }

        return $customerOptions[0] ?? null;
    }

    /**
     * @param CustomerOptionInterface[] $customerOptions
     */
    private function findCustomerOption(array $customerOptions, mixed $code): ?CustomerOptionInterface
    {
        foreach ($customerOptions as $customerOption) {
            if ($customerOption->getCode() === $code) {
                return $customerOption;
            }
        }

        return null;
    }

    private function getComparators(?CustomerOptionInterface $customerOption): array
    {
        if ($customerOption === null) {
            return ConditionComparatorEnum::getConstList();
        }

        return ConditionComparatorEnum::getValuesForCustomerOptionType($customerOption->getType());
    }

    private function fitsValueField(mixed $value, string $optionType): bool
    {
        $expectsArray = CustomerOptionTypeEnum::isSelect($optionType) || CustomerOptionTypeEnum::isDate($optionType);

        return $value === null || is_array($value) === $expectsArray;
    }

    private function addComparatorAndValueFields(
        FormInterface $form,
        ?CustomerOptionInterface $customerOption,
        bool $resetValue = false,
    ): void {
        $customerOptionType = $customerOption?->getType() ?? CustomerOptionTypeEnum::TEXT;
        [$formType, $formOptions] = ConditionComparatorEnum::getFormTypeForCustomerOptionType($customerOptionType);

        if ($customerOption !== null) {
            if (CustomerOptionTypeEnum::isSelect($customerOptionType)) {
                $formOptions['choices'] = $customerOption->getValues()->getValues();
                $formOptions['choice_label'] = 'name';
                $formOptions['choice_value'] = 'code';
            }

            $formOptions['required'] = $customerOptionType !== CustomerOptionTypeEnum::BOOLEAN;
        }

        $form->add('comparator', ChoiceType::class, [
            'label' => 'brille24.form.validators.fields.comparator',
            'choices' => array_flip(
                ConditionComparatorEnum::transformToTranslateArray($this->getComparators($customerOption)),
            ),
        ]);

        $valueOptions = [
            'label' => false,
            'field_type' => $formType,
            'field_options' => $formOptions,
            'option_type' => $customerOptionType,
        ];
        if ($resetValue) {
            // The stored value belongs to the previous customer option and cannot be transformed for the new one
            $valueOptions['data'] = null;
        }

        $form->add('value', ValueType::class, $valueOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Condition::class,
        ]);

        $resolver->setDefined('customerOptionGroup');
        $resolver->setAllowedTypes('customerOptionGroup', CustomerOptionGroupInterface::class);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_options_plugin_validator_condition';
    }
}
