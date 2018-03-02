<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Service;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\{
    CustomerOption, CustomerOptionInterface, CustomerOptionValueInterface
};
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\CustomerOptionsPlugin\Services\OrderPricesRecalculator;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface as Brille24OrderItem;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;

class OrderPricesRecalculatorTest extends TestCase
{
    /** @var OrderPricesRecalculator */
    private $priceRecalculator;

    /** @var array CustomerOptionValues[] */
    private $customerOptionValues = [];

    /** @var int */
    private $updateCount = 0;

    private $nameUpdate = 'no-update';

    //<editor-fold desc="Helper function for setup">
    public function setUp()
    {
        $this->priceRecalculator = new OrderPricesRecalculator();
    }

    private function createOrder(array $orderItems): OrderInterface
    {
        $order = self::createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection($orderItems));

        return $order;
    }

    private function createOrderItemOption(
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        bool $stillExists
    ): OrderItemOptionInterface {
        $orderItemOption = self::createMock(OrderItemOptionInterface::class);
        $orderItemOption->method('getCustomerOption')->willReturn($customerOption);
        $orderItemOption->method('getCustomerOptionValue')->willReturnCallback(
            function () use ($stillExists, $customerOptionValue) {
                return $stillExists ? $customerOptionValue : null;
            })
        ;

        $orderItemOption->method('setCustomerOptionValue')->willReturnCallback(
            function (CustomerOptionValueInterface $value) {
                $this->nameUpdate = $value->getName();
                $this->updateCount++;
            })
        ;

        return $orderItemOption;
    }

    private function createCustomerOptionValue(array $config): CustomerOptionValueInterface
    {
        $customerOptionValue = self::createMock(CustomerOptionValueInterface::class);
        $customerOptionValue->method('getCode')->willReturn($config['code']);
        $customerOptionValue->method('getName')->willReturn(isset($config['name']) ? $config['name'] : null);

        return $customerOptionValue;
    }

    /**
     * @param OrderItemOptionInterface|OrderItemOptionInterface[] $orderItemOption
     *
     * @return Brille24OrderItem
     */
    private function createOrderItem($orderItemOption): Brille24OrderItem
    {
        $orderItem = self::createMock(Brille24OrderItem::class);

        if (!is_array($orderItemOption)) {
            $orderItemOption = [$orderItemOption];
        }
        $orderItem->method('getCustomerOptionConfiguration')->willReturn($orderItemOption);

        return $orderItem;
    }
    //</editor-fold>

    /** @dataProvider dataItemsDontNeedProcessing */
    public function testNoItem(array $elements): void
    {
        $order = $this->createOrder($elements);

        $this->priceRecalculator->process($order);

        self::assertEquals(0, $this->updateCount);
    }

    public function dataItemsDontNeedProcessing(): array
    {
        return [
            'no items'         => [[]],
            'sylius base item' => [[new OrderItem()]],
        ];
    }

    public function testUpdate(): void
    {
        $orderItemOption =
            $this->createOrderItemOption(
                new CustomerOption(),
                $this->createCustomerOptionValue(['code' => 'hello', 'name' => 'something']),
                true
            );

        $order = $this->createOrder([$this->createOrderItem($orderItemOption)]);
        $this->priceRecalculator->process($order);

        self::assertEquals(1, $this->updateCount);
        self::assertEquals('something', $this->nameUpdate);
    }

    public function testBrokenUpdate(): void
    {
        $orderItemOption =
            $this->createOrderItemOption(
                new CustomerOption(),
                $this->createCustomerOptionValue(['code' => 'hello',]),
                false
            );

        $order = $this->createOrder([$this->createOrderItem($orderItemOption)]);
        $this->priceRecalculator->process($order);

        self::assertEquals(0, $this->updateCount);
        self::assertEquals('no-update', $this->nameUpdate);
    }
}
