<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, ChoiceType, CollectionType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('option_associations', CollectionType::class, [
                'required' => false,
                'label' => false,
                'entry_type' => CustomerOptionAssociationType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerOptionGroup::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_group_option';
    }
}