<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class ValidatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conditions', CollectionType::class, [
                'entry_type' => ConditionType::class,
                'allow_add' => true,
                'allow_delete' => true,

                'mapped' => false,
            ])
            ->add('constraints', CollectionType::class, [
                'entry_type' => ConditionType::class,
                'allow_add' => true,
                'allow_delete' => true,

                'mapped' => false,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'brille24_customer_options_plugin_validator';
    }
}