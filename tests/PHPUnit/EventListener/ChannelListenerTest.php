<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\EventListener\ChannelListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;

class ChannelListenerTest extends TestCase
{
    /** @var ChannelListener */
    private $channelCreateListener;

    private $customerOptionValue = [];

    private $persistCount = 0;

    //<editor-fold desc="Setup">
    public function setUp()
    {
        $this->channelCreateListener = new ChannelListener();
    }

    private function createArguments($entity): LifecycleEventArgs
    {
        $entityManger = self::createMock(EntityManagerInterface::class);
        $entityManger->method('persist')->willReturnCallback(function ($entity) {
            ++$this->persistCount;
        });

        $customerOptionValueRepository = self::createMock(EntityRepository::class);
        $customerOptionValueRepository->method('findAll')->willReturnCallback(function () {
            return $this->customerOptionValue;
        });

        $entityManger->method('getRepository')->willReturnCallback(
            function (string $entityClass) use ($customerOptionValueRepository) {
                self::assertEquals(CustomerOptionValue::class, $entityClass);

                return $customerOptionValueRepository;
            }
        );

        $arguments = self::createMock(LifecycleEventArgs::class);
        $arguments->method('getEntityManager')->willReturn($entityManger);
        $arguments->method('getEntity')->willReturn($entity);

        return $arguments;
    }

    private function createValue(): CustomerOptionValueInterface
    {
        $prices = new ArrayCollection();
        $value  = self::createMock(CustomerOptionValueInterface::class);
        $value->method('getPrices')->willReturnCallback(function () use ($prices) {
            return $prices;
        });
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
            'non entity'   => ['hello'],
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

        $channel   = new Channel();
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
