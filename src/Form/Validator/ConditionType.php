<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
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
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CustomerOptionGroupInterface $customerOptionGroup */
        $customerOptionGroup = $options['customerOptionGroup'];

        $builder
            ->add('customer_option', ChoiceType::class, [
                'label'        => 'brille24.form.validators.fields.customer_option',
                'choices'      => $customerOptionGroup->getOptions(),
                'choice_label' => 'name',
                'attr'         => ['onChange' => '$(event.target).parentsUntil("form").parent().submit();'],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            $condition = $event->getData() ?? new Condition();
            $customerOption = $condition->getCustomerOption();

            $comparatorChoices = ConditionComparatorEnum::getConstList();
            [$formType, $formOptions] = ConditionComparatorEnum::getFormTypeForCustomerOptionType('text');

            $customerOptionType = CustomerOptionTypeEnum::TEXT;

            if (
                ($condition instanceof ConditionInterface || $condition instanceof ConstraintInterface)
                && $customerOption instanceof CustomerOption
            ) {
                $customerOptionType = $customerOption->getType();
                $comparatorChoices = ConditionComparatorEnum::getValuesForCustomerOptionType($customerOptionType);

                [$formType, $formOptions] = ConditionComparatorEnum::getFormTypeForCustomerOptionType($customerOptionType);

                if (CustomerOptionTypeEnum::isSelect($customerOptionType)) {
                    $formOptions['choices'] = $customerOption->getValues()->getValues();
                    $formOptions['choice_label'] = 'name';
                }

                $formOptions['required'] = $customerOptionType !== CustomerOptionTypeEnum::BOOLEAN;
            }

            $form->add('comparator', ChoiceType::class, [
                'label'   => 'brille24.form.validators.fields.comparator',
                'choices' => array_flip(
                    ConditionComparatorEnum::transformToTranslateArray($comparatorChoices)
                ),
            ]);

            $form->add('value', ValueType::class, [
                'label'         => false,
                'field_type'    => $formType,
                'field_options' => $formOptions,
                'option_type'   => $customerOptionType,
            ]);
        });
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
