<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Form\Extensions;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeExtension extends AbstractTypeExtension
{
    public function __construct() { }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('customerOptionGroup', EntityType::class, [
            'class' => CustomerOptionGroup::class,
            'placeholder' => 'Please choose',
            'empty_data' => null,
            'required' => false
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