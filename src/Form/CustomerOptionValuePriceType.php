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

namespace Brille24\SyliusCustomerOptionsPlugin\Form;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerOptionValuePriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('channel', CustomerOptionValuePriceChannelType::class, [
                'required' => false,
                'attr'     => [
                    'readonly' => true,
                ],
                'disabled' => true,
                'label'    => 'sylius.ui.channel',
            ])
            ->add('percent', PercentType::class, [
                'empty_data' => '0.00',
                'scale'      => 5,
                'required'   => false,
                'label'      => 'sylius.ui.percent',
            ])
            ->add('amount', MoneyType::class, [
                'empty_data' => '0.00',
                'required'   => false,
                'label'      => 'sylius.ui.amount',
            ])
            ->add('type', ChoiceType::class, [
                'choices'      => CustomerOptionValuePrice::getAllTypes(),
                'choice_label' => function ($option) {
                    return 'brille24.ui.pricing.'.strtolower($option);
                },
                'label' => 'sylius.ui.type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerOptionValuePrice::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_customer_option_value_price';
    }
}
