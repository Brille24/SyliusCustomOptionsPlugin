<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\DataMapper;

use Brille24\SyliusCustomerOptionsPlugin\DataMapper\ProductListPriceImportDataMapper;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\FormInterface;

class ProductListPriceImportDataMapperTest extends TestCase
{
    public function testMapsEveryProductAndValueToANewPriceRow(): void
    {
        $customerOption = $this->createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('color');

        $customerOptionValue = $this->createMock(CustomerOptionValueInterface::class);
        $customerOptionValue->method('getCode')->willReturn('red');
        $customerOptionValue->method('getCustomerOption')->willReturn($customerOption);

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('WEB');

        $forms = new \ArrayIterator([
            'products' => $this->createForm(['tshirt', 'mug']),
            'customer_option_value_price' => $this->createForm([
                'dateValid' => null,
                'channel' => $channel,
                'customerOptionValues' => [$customerOptionValue],
                'type' => 'FIXED_AMOUNT',
                'amount' => 150,
                'percent' => 0.0,
            ]),
        ]);

        $viewData = null;
        (new ProductListPriceImportDataMapper())->mapFormsToData($forms, $viewData);

        $expectedRow = [
            'customer_option_code' => 'color',
            'customer_option_value_code' => 'red',
            'channel_code' => 'WEB',
            'valid_from' => null,
            'valid_to' => null,
            'type' => 'FIXED_AMOUNT',
            'amount' => 150,
            'percent' => 0.0,
        ];

        $expected = [
            ['product_code' => 'tshirt'] + $expectedRow,
            ['product_code' => 'mug'] + $expectedRow,
        ];

        self::assertEquals($expected, $viewData);
    }

    private function createForm(mixed $data): FormInterface
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn($data);

        return $form;
    }
}
