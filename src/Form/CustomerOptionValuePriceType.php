<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 16:22
 */

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerOptionValuePriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerOptionValueName', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
                'label' => false,
            ])
            ->add('channel', CustomerOptionValuePriceChannelType::class, [
                'required' => false,
                'attr' => [
                    'readonly' => true,
                ],
                'disabled' => true,
            ])
            ->add('percent', NumberType::class, [
                'empty_data' => 0,
            ])
            ->add('amount', IntegerType::class, [
                'empty_data' => 0,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => CustomerOptionValuePrice::getAllTypes(),
                'choice_label' => function ($option) {
                    return $option;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOptionValuePrice::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'brille24_customer_option_value_price';
    }
}
