<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\EventListener\ChannelListener;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;

class ChannelListenerTest extends TestCase
{
    private \Brille24\SyliusCustomerOptionsPlugin\EventListener\ChannelListener $channelCreateListener;

    private array $customerOptionValue = [];

    private int $persistCount = 0;

    //<editor-fold desc="Setup">
    public function setUp(): void
    {
        $customerOptionValueFactory = self::createMock(CustomerOptionValuePriceFactoryInterface::class);
        $customerOptionValueFactory
            ->method('createNew')
            ->willReturn(self::createMock(CustomerOptionValuePriceInterface::class));
        $this->channelCreateListener = new ChannelListener($customerOptionValueFactory);
    }

    private function createArguments($entity): LifecycleEventArgs
    {
        $entityManger = self::createMock(EntityManagerInterface::class);
        $entityManger->method('persist')->willReturnCallback(function ($entity) {
            ++$this->persistCount;
        });

        $customerOptionValueRepository = self::createMock(EntityRepository::class);
        $customerOptionValueRepository->method('findAll')->willReturnCallback(fn () => $this->customerOptionValue);

        $entityManger->method('getRepository')->willReturnCallback(
            function (string $entityClass) use ($customerOptionValueRepository) {
                self::assertEquals(CustomerOptionValue::class, $entityClass);

                return $customerOptionValueRepository;
            },
        );

        $arguments = self::createMock(LifecycleEventArgs::class);
        $arguments->method('getEntityManager')->willReturn($entityManger);
        $arguments->method('getEntity')->willReturn($entity);

        return $arguments;
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
