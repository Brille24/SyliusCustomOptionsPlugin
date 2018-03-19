<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Condition;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\ConditionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var ConditionInterface $configuration */
            $configuration = $event->getData();

            $comparatorChoices = ConditionComparatorEnum::getConstList();
            $valueConfig = ConditionComparatorEnum::getFormTypeForCustomerOptionType('text');

            if($configuration !== null){
                $customerOptionType = $configuration->getCustomerOption()->getType();

                $comparatorChoices = ConditionComparatorEnum::getValuesForCustomerOptionType($customerOptionType);

                $valueConfig = ConditionComparatorEnum::getFormTypeForCustomerOptionType($customerOptionType);

                if(CustomerOptionTypeEnum::isSelect($customerOptionType)){
                    $valueConfig[1]['choices'] = $configuration->getCustomerOption()->getValues()->getValues();
                    $valueConfig[1]['choice_label'] = 'name';
                }
            }

            $form->add('comparator', ChoiceType::class, [
                'choices' => array_flip(
                    ConditionComparatorEnum::transformToTranslateArray($comparatorChoices)
                ),
            ]);

            $form->add('value', $valueConfig[0], $valueConfig[1]);
        });

        /** @var CustomerOptionGroupInterface $customerOptionGroup */
        $customerOptionGroup = $options['customerOptionGroup'];

        $builder
            ->add('customer_option', ChoiceType::class, [
                'choices' => $customerOptionGroup->getOptions(),
                'choice_label' => 'name',
            ]);
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