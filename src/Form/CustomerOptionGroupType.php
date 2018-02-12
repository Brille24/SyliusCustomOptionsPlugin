<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, ChoiceType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerOptionGroupType extends AbstractResourceType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'sylius.ui.code'
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CustomerOptionGroupTranslationType::class,
                'label'      => 'brille24.form.customer_option_groups.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_group_option';
    }
}