<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Service;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface as Brille24OrderItem;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValuePriceRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionValueRefresher;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductInterface;

class CustomerOptionValueRefresherTest extends TestCase
{
    private CustomerOptionValueRefresher $customerOptionValueRefresher;

    private int $updateCount = 0;

    private string $nameUpdate = 'no-update';

    private int $priceUpdate;

    /** @var ChannelInterface|MockObject */
    private $channel;

    //<editor-fold desc="Helper function for setup">
    /** @var CustomerOptionValuePriceRepositoryInterface|MockObject */
    private $customerOptionValuePriceRepository;

    public function setUp(): void
    {
        $this->channel = self::createMock(ChannelInterface::class);

        $this->customerOptionValuePriceRepository = $this->createMock(
            CustomerOptionValuePriceRepositoryInterface::class,
        );

        $this->customerOptionValueRefresher = new CustomerOptionValueRefresher($this->customerOptionValuePriceRepository);
    }

    private function createOrder(array $orderItems): OrderInterface
    {
        $order = self::createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection($orderItems));
        $order->method('getChannel')->willReturn($this->channel);

        return $order;
    }

    private function createOrderItemOption(
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        bool $stillExists,
    ): OrderItemOptionInterface {
        $orderItemOption = self::createMock(OrderItemOptionInterface::class);
        $orderItemOption->method('getCustomerOption')->willReturn($customerOption);
        $orderItemOption->method('getCustomerOptionValue')->willReturnCallback(
            fn () => $stillExists ? $customerOptionValue : null,
        );

        $orderItemOption->method('setCustomerOptionValue')->willReturnCallback(
            function (CustomerOptionValueInterface $value) {
                $this->nameUpdate = $value->getName();
                ++$this->updateCount;
            },
        );

        $orderItemOption->method('setPrice')->willReturnCallback(
            function (CustomerOptionValuePriceInterface $price) {
                $this->priceUpdate = $price->getAmount();
                ++$this->updateCount;
            },
        );

        return $orderItemOption;
    }

    private function createCustomerOptionValue(array $config): CustomerOptionValueInterface
    {
        $price = $config['price'] ?? 0;

        $customerOptionValue = self::createMock(CustomerOptionValueInterface::class);
        $customerOptionValue->method('getCode')->willReturn($config['code']);
        $customerOptionValue->method('getName')->willReturn($config['name'] ?? null);

        $this->customerOptionValuePriceRepository->method('getPriceForChannel')->willReturnCallback(
            function (ChannelInterface $channel) use ($price) {
                $customerOptionPrice = self::createMock(CustomerOptionValuePriceInterface::class);
                $customerOptionPrice->method('getAmount')->willReturn($price);

                return $customerOptionPrice;
            },
        );

        return $customerOptionValue;
    }

    /**
     * @param OrderItemOptionInterface|OrderItemOptionInterface[] $orderItemOption
     */
    private function createOrderItem($orderItemOption): Brille24OrderItem
    {
        $orderItem = self::createMock(Brille24OrderItem::class);
        $product = self::createMock(ProductInterface::class);

        if (!is_array($orderItemOption)) {
            $orderItemOption = [$orderItemOption];
        }
        $orderItem->method('getCustomerOptionConfiguration')->willReturn($orderItemOption);
        $orderItem->method('getProduct')->willReturn($product);

        return $orderItem;
    }

    //</editor-fold>

    /** @dataProvider dataItemsDontNeedProcessing */
    public function testNoItem(array $elements): void
    {
        $order = $this->createOrder($elements);

        $this->customerOptionValueRefresher->process($order);

        self::assertEquals(0, $this->updateCount);
    }

    public function dataItemsDontNeedProcessing(): array
    {
        return [
            'no items' => [[]],
            'sylius base item' => [[new OrderItem()]],
        ];
    }

    public function testUpdateForSingleItem(): void
    {
        $orderItemOption = $this->createOrderItemOption(
            new CustomerOption(),
            $this->createCustomerOptionValue(['code' => 'hello', 'name' => 'something', 'price' => 10]),
            true,
        );

        $this->customerOptionValueRefresher->copyOverValuesForOrderItem(
            $this->createOrderItem($orderItemOption),
            $this->channel,
        );

        self::assertEquals(2, $this->updateCount);
        self::assertEquals('something', $this->nameUpdate);
        self::assertEquals(10, $this->priceUpdate);
    }

    public function testUpdate(): void
    {
        $orderItemOption = $this->createOrderItemOption(
            new CustomerOption(),
            $this->createCustomerOptionValue(['code' => 'hello', 'name' => 'something', 'price' => 10]),
            true,
        );

        $order = $this->createOrder([$this->createOrderItem($orderItemOption)]);
        $this->customerOptionValueRefresher->process($order);

        self::assertEquals(2, $this->updateCount);
        self::assertEquals('something', $this->nameUpdate);
        self::assertEquals(10, $this->priceUpdate);
    }

    public function testBrokenUpdate(): void
    {
        $orderItemOption = $this->createOrderItemOption(
            new CustomerOption(),
            $this->createCustomerOptionValue(['code' => 'hello']),
            false,
        );

        $order = $this->createOrder([$this->createOrderItem($orderItemOption)]);
        $this->customerOptionValueRefresher->process($order);

        self::assertEquals(0, $this->updateCount);
        self::assertEquals('no-update', $this->nameUpdate);
    }
}
