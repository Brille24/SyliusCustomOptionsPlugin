<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form\Extensions;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Form\Product\CustomerOptionValuePriceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProductTypeExtension used for the product form in the backend to add customer option groups
 *
 * @package Brille24\CustomerOptionsPlugin\Form\Extensions
 */
final class ProductTypeExtension extends AbstractTypeExtension
{
    public function __construct() { }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ProductInterface $product */
        $product = $options['data'];

        /** @var CustomerOptionInterface[] $customerOptions */
        $customerOptions = [];

        foreach ($product->getCustomerOptionGroup()->getOptionAssociations() as $optionAssociation){
            $customerOptions[] = $optionAssociation->getOption();
        }

        $prices = $product->getCustomerOptionPrices();

        $builder
            ->add('customerOptionGroup', EntityType::class, [
                'class' => CustomerOptionGroup::class,
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
            ])
        ;

        $builder->add('customerOptionPrices', CollectionType::class, [
            'entry_type' => CustomerOptionValuePriceType::class,
            'entry_options' => [
                'product' => $product,
            ],
            'label' => 'Customer Option Value Prices',
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ]);
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