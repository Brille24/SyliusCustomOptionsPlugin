<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form;

use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerOptionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Array keys are the constants and the values are the translations
        $possibleTypes = CustomerOptionTypeEnum::getTranslateArray();

        $builder
            ->add('code', TextType::class, [
                'label'      => 'sylius.ui.code',
                'empty_data' => '',
            ])
            ->add('type', ChoiceType::class, [
                'label'   => 'sylius.ui.type',
                'choices' => array_flip($possibleTypes),
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'brille24.ui.required',
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
                'by_reference' => false,
            ])
            ->add('configuration', CustomerOptionConfigurationType::class, [
                'label' => false,
            ])
        ;

        $builder->get('values')->addModelTransformer(new CallbackTransformer(
            function ($a) {
                if ($a instanceof Collection) {
                    return $a->toArray();
                }

                return $a;
            },
            function ($a) {
                return $a;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option';
    }
}
