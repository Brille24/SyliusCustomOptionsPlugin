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
    private \Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItem $orderItem;

    public function setUp(): void
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
        $item1->method('equals')->willReturnCallback(fn ($otherItem) => $otherItem === $item1);

        $item2 = $this->createMock(SyliusOrderItemInterface::class);

        $productWithCustomerOptions = $this->createMock(ProductInterface::class);
        $productWithCustomerOptions->method('hasCustomerOptions')->willReturn(true);

        $orderItemWithCustomOption = $this->createMock(OrderItemInterface::class);
        $orderItemWithCustomOption->method('getProduct')->willReturn($productWithCustomerOptions);

        $orderItemWithCustomOption2 = $this->createMock(OrderItemInterface::class);
        $orderItemWithCustomOption2->method('getProduct')->willReturn($productWithCustomerOptions);

        return [
            'two identical items'           => [$item1, $item1, true],
            'one item with custom options'  => [$orderItemWithCustomOption, $item2, false],
            'two items with custom options' => [$orderItemWithCustomOption, $orderItemWithCustomOption2, false],
        ];
    }
}
