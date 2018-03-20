<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form\Validator;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Condition;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\ConditionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CustomerOptionGroupInterface $customerOptionGroup */
        $customerOptionGroup = $options['customerOptionGroup'];

        $builder
            ->add('customer_option', ChoiceType::class, [
                'choices' => $customerOptionGroup->getOptions(),
                'choice_label' => 'name',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            $configuration = $event->getData();

            $comparatorChoices = ConditionComparatorEnum::getConstList();
            [$formType, $formOptions] = ConditionComparatorEnum::getFormTypeForCustomerOptionType('text');

            $customerOptionType = CustomerOptionTypeEnum::TEXT;

            if($configuration instanceof ConditionInterface){
                $customerOptionType = $configuration->getCustomerOption()->getType();
                $comparatorChoices = ConditionComparatorEnum::getValuesForCustomerOptionType($customerOptionType);

                [$formType, $formOptions] = ConditionComparatorEnum::getFormTypeForCustomerOptionType($customerOptionType);

                if(CustomerOptionTypeEnum::isSelect($customerOptionType)){
                    $formOptions['choices'] = $configuration->getCustomerOption()->getValues()->getValues();
                    $formOptions['choice_label'] = 'name';
                }

                $formOptions['required'] = $customerOptionType !== CustomerOptionTypeEnum::BOOLEAN;
            }

            $form->add('comparator', ChoiceType::class, [
                'choices' => array_flip(
                    ConditionComparatorEnum::transformToTranslateArray($comparatorChoices)
                ),
            ]);

            $form->add('value', ValueType::class, [
                'label' => false,
                'field_type' => $formType,
                'field_options' => $formOptions,
                'option_type' => $customerOptionType,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Condition::class,
        ]);

        $resolver->setDefined('customerOptionGroup');
        $resolver->setAllowedTypes('customerOptionGroup', CustomerOptionGroupInterface::class);
    }

    public function getBlockPrefix()
    {
        return 'brille24_customer_options_plugin_validator_condition';
    }

}