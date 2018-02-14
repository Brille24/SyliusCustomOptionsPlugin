<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 11:23
 */

namespace Brille24\CustomerOptionsPlugin\Form\Product;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerOptionValuePriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('percent', NumberType::class, [

            ])
            ->add('amount', IntegerType::class, [

            ])
            ->add('type', TextType::class, [

            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'brille24_product_value_price';
    }
}