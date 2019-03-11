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

use Brille24\SyliusCustomerOptionsPlugin\Form\Validator\ValidatorType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerOptionGroupType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'sylius.ui.code',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CustomerOptionGroupTranslationType::class,
                'label'      => 'brille24.form.customer_option_groups.translations',
            ])
            ->add('option_associations', CollectionType::class, [
                'entry_type'          => CustomerOptionAssociationType::class,
                'allow_add'           => true,
                'allow_delete'        => true,
                'label'               => false,
                'by_reference'        => false,
                'button_add_label'    => 'brille24.form.customer_option_groups.buttons.add_option',
                'button_delete_label' => 'brille24.form.customer_option_groups.buttons.delete_option',
            ])
            ->add('validators', CollectionType::class, [
                'entry_type'    => ValidatorType::class,
                'entry_options' => [
                    'customerOptionGroup' => $options['data'],
                ],
                'allow_add'           => true,
                'allow_delete'        => true,
                'label'               => false,
                'by_reference'        => false,
                'button_add_label'    => 'brille24.form.customer_option_groups.buttons.add_validator',
                'button_delete_label' => 'brille24.form.customer_option_groups.buttons.delete_validator',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'brille24_customer_group_option';
    }
}
