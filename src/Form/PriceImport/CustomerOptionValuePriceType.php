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

namespace Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Sonata\CoreBundle\Form\Type\DateTimeRangeType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerOptionValuePriceType extends AbstractType
{
    /** @var CustomerOptionRepositoryInterface */
    protected $customerOptionRepository;

    public function __construct(CustomerOptionRepositoryInterface $customerOptionRepository)
    {
        $this->customerOptionRepository = $customerOptionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $values = [];
        /** @var CustomerOptionInterface $customerOption */
        foreach ($this->customerOptionRepository->findAll() as $customerOption) {
            if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
                $values = array_merge($values, $customerOption->getValues()->getValues());
            }
        }

        $builder
            ->add('customerOptionValue', ChoiceType::class, [
                'choices'      => $values,
                'choice_label' => 'name',
                'group_by'     => function (CustomerOptionValueInterface $customerOptionValue) {
                    return $customerOptionValue->getCustomerOption()->getName();
                },
            ])
            ->add('channel', ChannelChoiceType::class, [
                'choice_attr' => static function (?ChannelInterface $channel) {
                    if ($channel !== null) {
                        if ($channel->getBaseCurrency() !== null) {
                            $currency = $channel->getBaseCurrency()->getCode() ?? 'EUR';
                        } else {
                            $currency  = 'EUR';
                        }
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
                'choice_label' => static function ($option) {
                    return 'brille24.ui.pricing.'.strtolower($option);
                },
            ])
            ->add('dateValid', DateTimeRangeType::class, [
                'required' => false,
                'label'    => 'Active range',
            ]);

        $this->addModelTransformer($builder);
    }

    private function addModelTransformer(FormBuilderInterface $builder): void
    {
        $builder->get('dateValid')->addModelTransformer(
            new CallbackTransformer(
                static function (?DateRange $dateRange) {
                    if ($dateRange === null) {
                        return [];
                    }

                    return [
                        'start' => $dateRange->getStart(),
                        'end'   => $dateRange->getEnd(),
                    ];
                },
                static function (array $dateTime) {
                    if ($dateTime['start'] === null || $dateTime['end'] === null) {
                        return null;
                    }

                    return new DateRange($dateTime['start'], $dateTime['end']);
                }
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                  'data_class' => CustomerOptionValuePrice::class,
              ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'brille24_product_value_price';
    }
}
