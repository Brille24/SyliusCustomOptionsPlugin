<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 12:16
 */

namespace Brille24\CustomerOptionsPlugin\Form\Product;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerOptionValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['readonly' => true],
                'label' => false,
            ])
            ->add('price', CustomerOptionValuePriceType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOptionValue::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'brille24_product_customer_option_value';
    }
}