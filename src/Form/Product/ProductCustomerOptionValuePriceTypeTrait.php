<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\Product;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Form\DateRangeType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Currencies;

trait ProductCustomerOptionValuePriceTypeTrait
{
    /**
     * @param FormBuilderInterface $builder
     * @param CustomerOptionValueInterface[] $customerOptionValues
     */
    private function addValuePriceFields(FormBuilderInterface $builder, array $customerOptionValues): void
    {
        $builder
            ->add('customerOptionValue', ChoiceType::class, [
                'choices'      => $customerOptionValues,
                'choice_label' => 'name',
                'group_by'     => static function (CustomerOptionValueInterface $customerOptionValue): ?string {
                    return $customerOptionValue->getCustomerOption()->getName();
                },
            ])
            ->add('channel', ChannelChoiceType::class, [
                'choice_attr' => static function (?ChannelInterface $channel) {
                    if ($channel !== null) {
                        $currency = $channel->getBaseCurrency() !== null ? $channel->getBaseCurrency()->getCode() ?? 'EUR' : 'EUR';
                        $symbol = Currencies::getSymbol($currency, 'en');

                        return ['data-attribute' => $symbol];
                    }

                    return '';
                },
                'attr' => ['onChange' => 'customerOptions.changeCustomerAmountCurrencyOnChannelChange(this);'],
            ])
            ->add('percent', PercentType::class, [
                'empty_data' => '0.00',
                'scale'      => 5,
                'required'   => false,
            ])
            ->add('amount', MoneyType::class, [
                'empty_data' => '0.00',
                'currency'   => 'USD',
                'required'   => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices'      => CustomerOptionValuePrice::getAllTypes(),
                'choice_label' => static function (string $option): string {
                    return 'brille24.ui.pricing.'.strtolower($option);
                },
            ])
            ->add('dateValid', DateRangeType::class, [
                'required'      => false,
                'label'         => 'Active range',
                'field_options' => [
                    'date_widget' => 'single_text',
                    'time_widget' => 'single_text',
                ],
            ])
        ;
    }
}
