<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\EventListener\CustomerOptionValueListener;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerOptionValueListenerTest extends TestCase
{
    /** @var ChannelInterface[] */
    private $channels = [];

    /** @var int */
    private $pricesAdded = 0;

    /** @var CustomerOptionValueListener */
    private $customerOptionValueListener;

    protected function setUp(): void
    {
        $channelRepository = $this->createMock(EntityRepository::class);
        $channelRepository->method('findAll')->willReturnCallback(function () {
            return $this->channels;
        });

        $customerOptionValuePriceFactory = $this->createMock(CustomerOptionValuePriceFactoryInterface::class);
        $customerOptionValuePriceFactory
            ->method('createNew')
            ->willReturn($this->createMock(CustomerOptionValuePriceInterface::class));

        $this->customerOptionValueListener = new CustomerOptionValueListener(
            $channelRepository,
            $customerOptionValuePriceFactory
        );
    }

    private function createArguments($entity): LifecycleEventArgs
    {
        $entityManger = $this->createMock(EntityManagerInterface::class);
        $entityManger->method('persist')->willReturnCallback(function ($entity) {
            ++$this->pricesAdded;
        });

        $arguments = $this->createMock(LifecycleEventArgs::class);
        $arguments->method('getEntityManager')->willReturn($entityManger);
        $arguments->method('getEntity')->willReturn($entity);

        return $arguments;
    }

    private function createEntity(array $prices): CustomerOptionValueInterface
    {
        $entity = $this->createMock(CustomerOptionValueInterface::class);
        $entity->method('getPrices')->willReturn(new ArrayCollection($prices));
        $entity->method('addPrice')->willReturnCallback(function ($price) {
            ++$this->pricesAdded;
        });

        return $entity;
    }

    public function testPrePersistWithIgnoredEntity()
    {
        $arguments = $this->createArguments(new CustomerOption());

        $this->customerOptionValueListener->prePersist($arguments);

        self::assertEquals(0, $this->pricesAdded);
    }

    public function testPrePersistWithEmptyChannels()
    {
        $arguments = $this->createArguments(new CustomerOptionValue());

        $this->customerOptionValueListener->prePersist($arguments);

        self::assertEquals(0, $this->pricesAdded);
    }

    public function testPrePersist()
    {
        $customerOptionValue = $this->createEntity([]);
        $arguments = $this->createArguments($customerOptionValue);
        $this->channels = [$this->createMock(ChannelInterface::class)];

        $this->customerOptionValueListener->prePersist($arguments);

        self::assertEquals(1, $this->pricesAdded);
    }

    public function testPrePersistWithExistingChannels()
    {
        $channel = $this->createMock(ChannelInterface::class);
        $price = $this->createMock(CustomerOptionValuePriceInterface::class);
        $price->method('getChannel')->willReturn($channel);

        $customerOptionValue = $this->createEntity([$price]);
        $arguments = $this->createArguments($customerOptionValue);
        $this->channels = [$channel];

        $this->customerOptionValueListener->prePersist($arguments);

        self::assertEquals(0, $this->pricesAdded);
    }
}
