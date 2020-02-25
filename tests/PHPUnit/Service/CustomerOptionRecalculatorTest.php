<?php
declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Core\Model\OrderItemInterface as SyliusOrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;

class CustomerOptionRecalculatorTest extends TestCase
{
    /** @var MockObject|AdjustmentFactoryInterface */
    public $adjustmentFactory;

    /** @var CustomerOptionRecalculator */
    public $customerOptionRecalculator;

    protected function setUp()
    {
        $this->adjustmentFactory = self::createMock(AdjustmentFactoryInterface::class);

        $this->customerOptionRecalculator = new CustomerOptionRecalculator($this->adjustmentFactory);
    }

    public function testingNoUpdateOnInvalidOrderItems(): void
    {
        $order = self::createMock(OrderInterface::class);
        $order
            ->method('getItems')
            ->willReturn(new ArrayCollection([
                self::createMock(SyliusOrderItemInterface::class),
            ]))
        ;

        $this->adjustmentFactory
            ->expects($this->never())
            ->method('createWithData')
        ;

        $this->customerOptionRecalculator->process($order);
    }

    public function testProcess(): void
    {
        $customerOption = self::createConfiguredMock(OrderItemOptionInterface::class, [
            'getCustomerOptionValue' => self::createMock(CustomerOptionValueInterface::class),
            'getCustomerOptionName'  => 'Test Adjustment',
            'getCalculatedPrice'     => 1200,
        ]);

        $adjustment = self::createMock(AdjustmentInterface::class);

        $orderItemUnit = self::createMock(OrderItemUnitInterface::class);
        $orderItemUnit->expects($this->once())->method('addAdjustment')->with($adjustment);

        $orderItem = self::createConfiguredMock(
            OrderItemInterface::class,
            [
                'getUnitPrice'                   => 1000,
                'getUnits'                       => new ArrayCollection([$orderItemUnit]),
                'getCustomerOptionConfiguration' => [$customerOption],
            ]
        );

        $order = self::createConfiguredMock(
            OrderInterface::class,
            ['getItems' => new ArrayCollection([$orderItem])]
        );

        $this->adjustmentFactory
            ->expects($this->once())
            ->method('createWithData')
            ->with('CUSTOMER_OPTION_ADJUSTMENT', 'Test Adjustment', 1200)
            ->willReturn($adjustment)
        ;

        $this->customerOptionRecalculator->process($order);
    }
}
