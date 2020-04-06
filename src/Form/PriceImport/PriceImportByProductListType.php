<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Form\PriceImport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PriceImportByProductListType extends AbstractType
{
    /** {@inheritdoc} */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('products', TextType::class)
            ->add('customer_option_value_price', CustomerOptionValuePriceType::class, [
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                'attr'  => [
                    'class' => 'ui primary button',
                ],
            ])
        ;

        $builder->get('products')->addModelTransformer(new CallbackTransformer(
            static function ($productArray) {
                if (!is_array($productArray)) {
                    return '';
                }

                return implode(', ', $productArray);
            },
            static function (string $productsAsString) {
                return explode(',', preg_replace('/\s+/', '', $productsAsString));
            }
        ));

        $builder->addModelTransformer(new CallbackTransformer(
            static function () {
                return null;
            },
            static function ($formData) {
                if (empty($formData)) {
                    return $formData;
                }

                // Build array usable by the importer
                // [['product_code', 'customer_option_code', 'customer_option_value_code', 'channel_code', 'valid_from', 'valid_to', 'type', 'amount', 'percent']]

                $valuePriceData = $formData['customer_option_value_price'];

                $dateValid   = $valuePriceData['dateValid'];
                $channelCode = $valuePriceData['channel']->getCode();

                $customerOptionValues = $valuePriceData['customerOptionValues'];
                $type                 = $valuePriceData['type'];
                $amount               = $valuePriceData['amount'];
                $percent              = $valuePriceData['percent'];

                $dateFrom = null;
                $dateTo   = null;
                if (null !== $dateValid) {
                    $dateFrom = $dateValid->getStart()->format(DATE_ATOM);
                    $dateTo   = $dateValid->getEnd()->format(DATE_ATOM);
                }

                $formattedData = [];
                foreach ($customerOptionValues as $customerOptionValue) {
                    $customerOptionCode      = $customerOptionValue->getCustomerOption()->getCode();
                    $customerOptionValueCode = $customerOptionValue->getCode();

                    foreach ($formData['products'] as $productCode) {
                        $formattedData[] = [
                            'product_code'               => $productCode,
                            'customer_option_code'       => $customerOptionCode,
                            'customer_option_value_code' => $customerOptionValueCode,
                            'channel_code'               => $channelCode,
                            'valid_from'                 => $dateFrom,
                            'valid_to'                   => $dateTo,
                            'type'                       => $type,
                            'amount'                     => $amount,
                            'percent'                    => $percent,
                        ];
                    }
                }

                return $formattedData;
            }
        ));
    }
}
