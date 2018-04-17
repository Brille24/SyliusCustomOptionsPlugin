<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomDateTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateOptions = $builder->get('date')->getOptions();

        $builder->add('date', CustomDateType::class, array_merge($dateOptions, ['label' => 'sylius.ui.date']));
    }

    public function getParent()
    {
        return DateTimeType::class;
    }

    public function getBlockPrefix()
    {
        return 'brille24_customer_options_plugin_custom_date_time_type';
    }
}
