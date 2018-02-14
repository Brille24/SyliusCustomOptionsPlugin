<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 09:17
 */

namespace Brille24\CustomerOptionsPlugin\Form\Extensions;


use Brille24\CustomerOptionsPlugin\Form\Product\ShopCustomerOptionType;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('customerOptions', ShopCustomerOptionType::class, [
            'product' => $options['product'],
        ]);
    }

    public function getExtendedType()
    {
        return AddToCartType::class;
    }
}