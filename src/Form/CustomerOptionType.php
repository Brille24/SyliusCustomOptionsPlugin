<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form;

use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\
{
    ChoiceType, TextType, CheckboxType
};
use Symfony\Component\Form\FormBuilderInterface;

class CustomerOptionType extends AbstractType
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
            ]);
    }
}