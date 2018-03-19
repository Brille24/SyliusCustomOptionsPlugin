<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Condition;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('comparator', ChoiceType::class, [
                'choices' => [
                    '>' => Condition::GREATER,
                    '>=' => Condition::GREATER_OR_EQUAL,
                    '=' => Condition::EQUAL,
                    '<=' => Condition::LESSER_OR_EQUAL,
                    '<' => Condition::LESSER,
                ],
            ])
            ->add('value', NumberType::class, [

            ])
        ;
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