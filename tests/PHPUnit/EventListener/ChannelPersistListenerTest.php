<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\EventListener\ChannelPersistListener;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;

class ChannelPersistListenerTest extends TestCase
{
    private ChannelPersistListener $channelCreateListener;

    private array $customerOptionValue = [];

    private int $persistCount = 0;

    //<editor-fold desc="Setup">
    public function setUp(): void
    {
        $customerOptionValueFactory = self::createMock(CustomerOptionValuePriceFactoryInterface::class);
        $customerOptionValueFactory
            ->method('createNew')
            ->willReturn(self::createMock(CustomerOptionValuePriceInterface::class))
        ;

        $customerOptionValueRepository = self::createMock(CustomerOptionValueRepositoryInterface::class);
        $customerOptionValueRepository
            ->method('findValuesWithoutPricingInChannel')
            ->willReturnCallback(fn () => $this->customerOptionValue);

        $this->channelCreateListener = new ChannelPersistListener(
            $customerOptionValueFactory,
            $customerOptionValueRepository,
        );
    }

    private function createArguments($entity): LifecycleEventArgs
    {
        $entityManger = self::createMock(EntityManagerInterface::class);
        $entityManger->method('persist')->willReturnCallback(function ($entity) {
            ++$this->persistCount;
        });

        return self::createConfiguredMock(LifecycleEventArgs::class, [
            'getObject'=>$entity,
            'getObjectManager'=>$entityManger,
        ]);
    }

    private function createValue(): CustomerOptionValueInterface
    {
        $prices = new ArrayCollection();
        $value = self::createMock(CustomerOptionValueInterface::class);
        $value->method('getPrices')->willReturnCallback(fn () => $prices);
        $value->method('addPrice')->willReturnCallback(function ($price) use (&$prices) {
            $prices->add($price);
        });

        return $value;
    }

    //</editor-fold>

    /** @dataProvider dataPrePersistWithWrongEntity */
    public function testPrePersistWithNonEntity($entity): void
    {
        $arguments = $this->createArguments($entity);

        $this->channelCreateListener->prePersist($arguments);

        self::assertEquals(0, $this->persistCount);
    }

    public function dataPrePersistWithWrongEntity(): array
    {
        return [
            'non entity' => ['hello'],
            'wrong entity' => [new CustomerOption()],
        ];
    }

    public function testPrePersistWithEmptyValuesRepository(): void
    {
        $arguments = $this->createArguments(new Channel());

        $this->channelCreateListener->prePersist($arguments);

        self::assertEquals(0, $this->persistCount);
    }

    public function testPrePersist(): void
    {
        $this->customerOptionValue = [$this->createValue()];

        $channel = new Channel();
        $arguments = $this->createArguments($channel);

        $this->channelCreateListener->prePersist($arguments);

        self::assertEquals(1, $this->persistCount);
        self::assertCount(1, $this->customerOptionValue[0]->getPrices());
    }

    public function testPrePersistWithMultiple(): void
    {
        $this->customerOptionValue = [$this->createValue(), $this->createValue()];

        $arguments = $this->createArguments(new Channel());

        $this->channelCreateListener->prePersist($arguments);

        self::assertEquals(2, $this->persistCount);
        foreach ($this->customerOptionValue as $customerOptionValue) {
            self::assertCount(1, $customerOptionValue->getPrices());
        }
    }
}
