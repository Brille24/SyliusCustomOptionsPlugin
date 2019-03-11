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

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Extensions;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\Product\CustomerOptionValuePriceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProductTypeExtension used for the product form in the backend to add customer option groups
 *
 * @method iterable getExtendedTypes()
 */
final class ProductTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ProductInterface $product */
        $product = $options['data'];

        /** @var CustomerOptionInterface[] $customerOptions */
        $customerOptions = [];

        $customerOptionGroup = $product->getCustomerOptionGroup();
        if ($customerOptionGroup !== null) {
            foreach ($customerOptionGroup->getOptionAssociations() as $optionAssociation) {
                $customerOptions[] = $optionAssociation->getOption();
            }
        }

        $builder
            ->add(
                'customer_option_group',
                EntityType::class,
                [
                'class'       => CustomerOptionGroup::class,
                'placeholder' => 'Please choose',
                'empty_data'  => null,
                'required'    => false,
            ]
            );

        $builder->add(
            'customer_option_value_prices',
            CollectionType::class,
            [
            'entry_type'    => CustomerOptionValuePriceType::class,
            'entry_options' => [
                'product' => $product,
            ],
            'label'         => false,
            'by_reference'  => false,
            'allow_add'     => true,
            'allow_delete'  => true,
        ]
        );
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType(): string
    {
        return ProductType::class;
    }
}
