<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 09:17
 */

namespace Brille24\CustomerOptionsPlugin\Form\Extensions;


use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Form\ProductCustomerOptionType;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('customerOptions', ProductCustomerOptionType::class, [
            'product' => $options['product'],
        ]);
    }

    public function getExtendedType()
    {
        return AddToCartType::class;
    }
}