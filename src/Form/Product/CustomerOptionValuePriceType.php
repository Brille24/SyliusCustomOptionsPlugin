<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 11:23
 */

namespace Brille24\CustomerOptionsPlugin\Form\Product;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\CustomerOptionsPlugin\Entity\Product;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
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
                'choices' => $values,
                'choice_label' => function (CustomerOptionValueInterface $option) {
                    return $option->getName();
                },
            ])
            ->add('channel', ChannelChoiceType::class, [
            ])
            ->add('percent', PercentType::class, [
                'empty_data' => 0,
                'scale' => 5
            ])
            ->add('amount', MoneyType::class, [
                'empty_data' => 0,
                'currency' => 'USD'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => CustomerOptionValuePrice::getAllTypes(),
                'choice_label' => function ($option) {
                    return 'brille24.ui.pricing.'.strtolower($option);
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
