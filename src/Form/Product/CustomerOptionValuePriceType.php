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

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Product;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerOptionValuePriceType extends AbstractType
{
    use ProductCustomerOptionValuePriceTypeTrait;

    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ProductInterface $product */
        $product = $options['product'];
        $values = [];

        /** @var CustomerOptionInterface $customerOption */
        foreach ($product->getCustomerOptions() as $customerOption) {
            if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
                $values = array_merge($values, $customerOption->getValues()->getValues());
            }
        }

        $this->addValuePriceFields($builder, $values);
    }

    /** {@inheritdoc} */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => CustomerOptionValuePrice::class])
            ->setDefined('product')
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    /** {@inheritdoc} */
    public function getBlockPrefix(): string
    {
        return 'brille24_product_value_price';
    }
}
