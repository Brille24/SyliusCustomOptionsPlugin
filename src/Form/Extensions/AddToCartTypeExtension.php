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

namespace Brille24\CustomerOptionsPlugin\Form\Extensions;

use Brille24\CustomerOptionsPlugin\Form\Product\ShopCustomerOptionType;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AddToCartTypeExtension extends the add to cart action in the front-end and adds customerOptions
 */
final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customerOptions', ShopCustomerOptionType::class, [
            'product' => $options['product'],
        ]);
    }

    public function getExtendedType(): string
    {
        return AddToCartType::class;
    }
}
