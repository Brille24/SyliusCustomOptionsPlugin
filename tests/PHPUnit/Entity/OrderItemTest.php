<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItem;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemInterface as SyliusOrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

class OrderItemTest extends TestCase
{
    /** @var OrderItem */
    private $orderItem;

    public function setUp()
    {
        $this->orderItem = new OrderItem();
    }

    private function createOrderItemUnit(OrderItemInterface $orderItem): OrderItemUnitInterface
    {
        $orderItemUnit = self::createMock(OrderItemUnitInterface::class);
        $orderItemUnit->method('getOrderItem')->willReturn($orderItem);

        return $orderItemUnit;
    }

    private function createCustomerOptionConfiguration(string $type, $amount): OrderItemOptionInterface
    {
        $orderItemOption = self::createMock(OrderItemOptionInterface::class);
        $orderItemOption->method('getPricingType')->willReturn($type);

        $customerOptionValue = self::createMock(CustomerOptionValueInterface::class);
        $orderItemOption->method('getCustomerOptionValue')->willReturn($customerOptionValue);
        switch ($type) {
            case CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT:
                $orderItemOption->method('getFixedPrice')->willReturn($amount);

                break;
            case CustomerOptionValuePriceInterface::TYPE_PERCENT:
                $orderItemOption->method('getPercent')->willReturn($amount);

                break;
            default:
                throw new Exception();
        }

        return $orderItemOption;
    }

    public function testGetSubtotalForUnconfiguredProduct(): void
    {
        $this->orderItem->setUnitPrice(10);
        $this->orderItem->addUnit($this->createOrderItemUnit($this->orderItem));

        self::assertEquals(10, $this->orderItem->getSubtotal());
    }

    /** @dataProvider dataGetSubtotalForConfiguredProductFixed */
    public function testGetSubtotalForConfiguredProductFixed(int $amount, int $expectedTotal): void
    {
        $configuration = [
            $this->createCustomerOptionConfiguration(CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT, 100),
        ];

        $this->orderItem->setCustomerOptionConfiguration($configuration);
        $this->orderItem->setUnitPrice(10);

        for ($i = 0; $i < $amount; ++$i) {
            $this->orderItem->addUnit($this->createOrderItemUnit($this->orderItem));
        }

        self::assertEquals($expectedTotal, $this->orderItem->getSubtotal());
    }

    public function dataGetSubtotalForConfiguredProductFixed(): array
    {
        return
            [
                'one item'  => [1, 110],
                'two items' => [2, 220],
            ];
    }

    public function testGetSubtotalForConfiguredProductPercent(): void
    {
        $configuration = [
            $this->createCustomerOptionConfiguration(CustomerOptionValuePriceInterface::TYPE_PERCENT, 0.32),
        ];

        $this->orderItem->setCustomerOptionConfiguration($configuration);
        $this->orderItem->setUnitPrice(10);

        $this->orderItem->addUnit($this->createOrderItemUnit($this->orderItem));

        // 10*0.32 rounded to the next bigger int
        self::assertEquals(13, $this->orderItem->getSubtotal());
    }

    public function testGetSubtotalForConfiguredProductMultiple(): void
    {
        $configuration = [
            $this->createCustomerOptionConfiguration(CustomerOptionValuePriceInterface::TYPE_FIXED_AMOUNT, 100),
            $this->createCustomerOptionConfiguration(CustomerOptionValuePriceInterface::TYPE_PERCENT, 0.32),
        ];

        $this->orderItem->setCustomerOptionConfiguration($configuration);
        $this->orderItem->setUnitPrice(10);

        $this->orderItem->addUnit($this->createOrderItemUnit($this->orderItem));

        // 10*0.32 rounded to the next bigger int
        self::assertEquals(113, $this->orderItem->getSubtotal());
    }

    /** @dataProvider dataItChecksIfTwoItemsAreEqual */
    public function testItChecksIfTwoItemsAreEqual(
        SyliusOrderItemInterface $item1,
        SyliusOrderItemInterface $item2,
        bool $equals
    ): void {
        $this->assertEquals($equals, $item1->equals($item2));
    }

    public function dataItChecksIfTwoItemsAreEqual(): array
    {
        $item1 = $this->createMock(SyliusOrderItemInterface::class);
        $item1->method('equals')->willReturnCallback(function ($otherItem) use ($item1) {
            return $otherItem === $item1;
        });

        $item2 = $this->createMock(SyliusOrderItemInterface::class);

        $productWithCustomerOptions = $this->createMock(ProductInterface::class);
        $productWithCustomerOptions->method('hasCustomerOptions')->willReturn(true);

        $orderItemWithCustomOption = $this->createMock(OrderItemInterface::class);
        $orderItemWithCustomOption->method('getProduct')->willReturn($productWithCustomerOptions);

        $orderItemWithCustomOption2 = $this->createMock(OrderItemInterface::class);
        $orderItemWithCustomOption2->method('getProduct')->willReturn($productWithCustomerOptions);

        return [
            'two identical items' => [$item1, $item1, true],
            'one item with custom options' => [$orderItemWithCustomOption, $item2, false],
            'two items with custom options' => [$orderItemWithCustomOption, $orderItemWithCustomOption2, false],
        ];
    }
}
