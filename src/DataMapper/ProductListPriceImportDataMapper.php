<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\DataMapper;

use Symfony\Component\Form\DataMapperInterface;

class ProductListPriceImportDataMapper implements DataMapperInterface
{
    /** {@inheritdoc} */
    public function mapDataToForms($viewData, $forms): void
    {
    }

    /** {@inheritdoc} */
    public function mapFormsToData($forms, &$viewData): void
    {
        $formData = iterator_to_array($forms);

        if (!array_key_exists('customer_option_value_price', $formData) || !array_key_exists('products', $formData)) {
            $viewData = $formData;

            return;
        }

        // Build array usable by the importer
        // [['product_code', 'customer_option_code', 'customer_option_value_code', 'channel_code', 'valid_from', 'valid_to', 'type', 'amount', 'percent']]

        $valuePriceData = $formData['customer_option_value_price']->getData();

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

            foreach ($formData['products']->getData() as $productCode) {
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

        $viewData = $formattedData;
    }
}
