<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_option', ChoiceType::class, [
                'choices' => ['a' => 1, 'b' => 2, 'c' => 3],
            ])
            ->add('operator', ChoiceType::class, [
                'choices' => ['>' => 1, '>=' => 2, '=' => 3, '<=' => 4, '<' => 5],
            ])
            ->add('value', NumberType::class, [

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'brille24_customer_options_plugin_validator_condition';
    }

}