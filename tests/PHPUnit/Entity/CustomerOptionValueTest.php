<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface as PriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerOptionValueTest extends TestCase
{
    /** @var CustomerOptionValue */
    private $customerOptionValue;

    public function setUp()
    {
        $this->customerOptionValue = new CustomerOptionValue();
    }

    //<editor-fold desc="Helper">
    private function createPrice(ChannelInterface $channel, int $price, ?ProductInterface $product = null)
    {
        $priceObject = self::createMock(PriceInterface::class);
        $priceObject->method('getAmount')->willReturn($price);
        $priceObject->method('getChannel')->willReturn($channel);
        $priceObject->method('getProduct')->willReturn($product);

        return $priceObject;
    }

    private function createChannel(int $id, string $code): ChannelInterface
    {
        $channel = self::createMock(ChannelInterface::class);
        $channel->method('getId')->willReturn($id);
        $channel->method('getCode')->willReturn($code);

        return $channel;
    }

    private function createDatedPrice(bool $isActive, ChannelInterface $channel): PriceInterface
    {
        $priceObject = self::createMock(PriceInterface::class);
        $priceObject->method('isActive')->willReturn($isActive);
        $priceObject->method('getChannel')->willReturn($channel);
        $priceObject->method('getProduct')->willReturn(self::createMock(ProductInterface::class));

        return $priceObject;
    }

    //</editor-fold>

    /** @dataProvider dataGetPriceForChannel */
    public function testGetPriceForChannelFound(array $prices, ChannelInterface $channel, int $price): void
    {
        $this->customerOptionValue->setPrices(new ArrayCollection($prices));

        $priceObject = $this->customerOptionValue->getPriceForChannel($channel, true);

        self::assertEquals($channel, $priceObject->getChannel());
        self::assertEquals($price, $priceObject->getAmount());
    }

    public function dataGetPriceForChannel(): array
    {
        $channel  = $this->createChannel(1, 'en_us');
        $channel1 = $this->createChannel(1, 'some_other_code');

        $otherChannel = $this->createChannel(2, 'de_DE');

        $createTestData = function (ChannelInterface $channel, int $price): array {
            $priceObject = $this->createPrice($channel, $price);

            return [[$priceObject], $channel, $price];
        };

        return [
            'one channel identical reference' => $createTestData($channel, 10),
            'one channel copy (id equal)'     => $createTestData($channel1, 20),
            'multiple channels (first)'       => [
                [$this->createPrice($channel1, 20), $this->createPrice($otherChannel, 30)],
                $channel,
                20,
            ],
            'multiple channels (last)' => [
                [$this->createPrice($otherChannel, 30), $this->createPrice($channel1, 20)],
                $channel,
                20,
            ],
        ];
    }

    public function testGetPriceForChannelChannelNotFound(): void
    {
        $channel = $this->createChannel(1, 'en_US');
        $price   = $this->createPrice($channel, 10);
        $this->customerOptionValue->setPrices(new ArrayCollection([$price]));

        $priceObject = $this->customerOptionValue->getPriceForChannel($this->createChannel(2, 'de_DE'));

        self::assertNull($priceObject);
    }

    /** @dataProvider dataGetPriceForChannelWithOverride */
    public function testGetPriceForChannelWithOverride(array $prices, ChannelInterface $channel, int $price): void
    {
        $this->customerOptionValue->setPrices(new ArrayCollection($prices));

        $priceObject = $this->customerOptionValue->getPriceForChannel($channel, true);

        self::assertEquals($price, $priceObject->getAmount());
    }

    public function dataGetPriceForChannelWithOverride(): array
    {
        $product = self::createMock(ProductInterface::class);
        $channel = self::createMock(ChannelInterface::class);

        return [
            'no override'   => [[$this->createPrice($channel, 10)], $channel, 10],
            'with override' => [
                [$this->createPrice($channel, 10), $this->createPrice($channel, 20, $product)],
                $channel,
                20,
            ],
        ];
    }

    /** @dataProvider dataActive */
    public function testActive(array $prices, ChannelInterface $channel, ?PriceInterface $price): void
    {
        $this->customerOptionValue->setPrices(new ArrayCollection($prices));

        $priceObject = $this->customerOptionValue->getPriceForChannel($channel);

        self::assertEquals($price, $priceObject);
    }

    public function dataActive(): array
    {
        $channel = $this->createChannel(1, 'en_us');

        $activePrice = $this->createDatedPrice(true, $channel);

        return [
            'no active price'  => [[$this->createDatedPrice(false, $channel)], $channel, null],
            'one active price' => [[$activePrice], $channel, $activePrice],
            'multiple prices'  => [
                [
                    $this->createDatedPrice(false, $channel),
                    $activePrice,
                ],
                $channel,
                $activePrice,
            ],
        ];
    }
}
