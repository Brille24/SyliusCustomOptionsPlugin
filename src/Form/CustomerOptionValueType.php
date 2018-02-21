<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerOptionValueType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('code', TextType::class)
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CustomerOptionValueTranslationType::class,
            ]);
        //=======
//            ])
//            ->add('value', TextType::class)
//        ;
//>>>>>>> customer_option_pricing
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaults = [
            'error_bubbling' => true,
            'data_class' => CustomerOptionValue::class,
        ];
        $resolver->setDefaults($defaults);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_value';
    }
}
