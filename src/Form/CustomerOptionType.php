<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, ChoiceType, CollectionType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerOptionType extends AbstractResourceType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Array keys are the constants and the values are the translations
        $possibleTypes = CustomerOptionTypeEnum::getTranslateArray();

        $builder
            ->add('code', TextType::class, [
                'label' => 'sylius.ui.code'
            ])
            ->add('type', ChoiceType::class, [
                'label'   => 'sylius.ui.type',
                'choices' => array_flip($possibleTypes),
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'brille24.ui.required'
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CustomerOptionTranslationType::class,
                'label'      => 'brille24.form.customer_options.translations',
            ])
            ->add('values', CollectionType::class, [
                'entry_type'   => CustomerOptionValueType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'by_reference' => false
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option';
    }
}