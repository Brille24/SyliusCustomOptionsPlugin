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

namespace Brille24\CustomerOptionsPlugin\Form\Product;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\CustomerOptionsPlugin\Entity\Product;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerOptionValuePriceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
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

        $builder
            ->add('customerOptionValue', ChoiceType::class, [
                'choices'      => $values,
                'choice_label' => function (CustomerOptionValueInterface $option) {
                    return $option->getName();
                },
            ])
            ->add('channel', ChannelChoiceType::class, [
                'choice_attr' => function (?ChannelInterface $channel) {
                    if ($channel !== null) {
                        return ['data-attribute' => $channel->getBaseCurrency()->getCode()];
                    }
                    return '';
                },
            ])
            ->add('percent', PercentType::class, [
                'empty_data' => 0,
                'scale'      => 5,
                'required' => false
            ])
            ->add('amount', MoneyType::class, [
                'empty_data' => 0,
                'currency'   => 'USD',
                'required' => false
            ])
            ->add('type', ChoiceType::class, [
                'choices'      => CustomerOptionValuePrice::getAllTypes(),
                'choice_label' => function ($option) {
                    return 'brille24.ui.pricing.' . strtolower($option);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                              'data_class' => CustomerOptionValuePrice::class,
                          ])
            ->setDefined('product')
            ->setAllowedTypes('product', Product::class)
        ;
    }

    public function getBlockPrefix()
    {
        return 'brille24_product_value_price';
    }
}
