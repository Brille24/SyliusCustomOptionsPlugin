<?php

declare(strict_types=1);

namespace Tests\Sylius\ShopApiPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator;
use Brille24\SyliusCustomerOptionsPlugin\Subscriber\SelectAdjustmentCalculatorSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface as SyliusOrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CustomerOptionRecalculatorTest extends TestCase
{
    /** @var MockObject|AdjustmentFactoryInterface */
    public $adjustmentFactory;

    /** @var CustomerOptionRecalculator */
    public $customerOptionRecalculator;

    protected function setUp(): void
    {
        $this->adjustmentFactory = self::createMock(AdjustmentFactoryInterface::class);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber(
            new SelectAdjustmentCalculatorSubscriber($this->adjustmentFactory),
        );

        $this->customerOptionRecalculator = new CustomerOptionRecalculator($eventDispatcher);
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
        $orderItemOption = self::createConfiguredMock(OrderItemOptionInterface::class, [
            'getCustomerOptionValue' => self::createMock(CustomerOptionValueInterface::class),
            'getCustomerOptionName' => 'Test Adjustment',
            'getCalculatedPrice' => 1200,
            'getCustomerOptionType' => 'select',
        ]);

        $adjustment = self::createMock(AdjustmentInterface::class);

        $orderItemUnit = self::createMock(OrderItemUnitInterface::class);
        $orderItemUnit->expects($this->once())->method('addAdjustment')->with($adjustment);

        $orderItem = self::createConfiguredMock(
            OrderItemInterface::class,
            [
                'getUnitPrice' => 1000,
                'getUnits' => new ArrayCollection([$orderItemUnit]),
                'getCustomerOptionConfiguration' => [$orderItemOption],
            ],
        );
        $orderItemOption->method('getOrderItem')->willReturn($orderItem);

        $order = self::createConfiguredMock(
            OrderInterface::class,
            ['getItems' => new ArrayCollection([$orderItem])],
        );

        $this->adjustmentFactory
            ->expects($this->once())
            ->method('createWithData')
            ->with('customer_option', 'Test Adjustment', 1200)
            ->willReturn($adjustment)
        ;

        $this->customerOptionRecalculator->process($order);
    }

    public function testProcessingAnOrderWithoutSuitableSubscribers(): void
    {
        $orderItemOption = self::createConfiguredMock(OrderItemOptionInterface::class, [
            'getCustomerOptionValue' => self::createMock(CustomerOptionValueInterface::class),
            'getCustomerOptionName' => 'Test Adjustment',
            'getCalculatedPrice' => 1200,
            'getCustomerOptionType' => 'text',
        ]);
        $orderItemOption->expects($this->never())->method('getOrderItem');

        $orderItem = self::createConfiguredMock(
            OrderItemInterface::class,
            [
                'getUnitPrice' => 1000,
                'getCustomerOptionConfiguration' => [$orderItemOption],
            ],
        );

        $order = self::createConfiguredMock(
            OrderInterface::class,
            ['getItems' => new ArrayCollection([$orderItem])],
        );

        $this->adjustmentFactory->expects($this->never())->method('createWithData');

        $this->customerOptionRecalculator->process($order);
    }
}
