<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form\Extensions;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Form\Product\CustomerOptionType;
use Brille24\CustomerOptionsPlugin\Form\Product\CustomerOptionValueType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeExtension extends AbstractTypeExtension
{
    public function __construct() { }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductInterface $product */
        $product = $options['data'];

        /** @var CustomerOptionInterface[] $customerOptions */
        $customerOptions = [];

        foreach ($product->getCustomerOptionGroup()->getOptionAssociations() as $optionAssociation){
            $customerOptions[] = $optionAssociation->getOption();
        }

        $builder
            ->add('customerOptionGroup', EntityType::class, [
                'class' => CustomerOptionGroup::class,
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
            ])
        ;

        $builder->add('customerOptions', CollectionType::class, [
            'entry_type' => CustomerOptionType::class,
            'label' => 'Customer Option Value Prices',
        ]);
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return ProductType::class;
    }
}