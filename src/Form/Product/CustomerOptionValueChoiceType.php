<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 20.02.18
 * Time: 13:01
 */

namespace Brille24\CustomerOptionsPlugin\Form\Product;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerOptionValueChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'brille24_product_value_choice';
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

}