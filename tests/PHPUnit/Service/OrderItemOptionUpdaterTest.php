<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Service;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\OrderItemOptionUpdater;
use Brille24\SyliusCustomerOptionsPlugin\Services\OrderItemOptionUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderItemOptionUpdaterTest extends TestCase
{
    /** @var CustomerOptionRepositoryInterface */
    private $customerOptionRepository;

    /** @var OrderItemOptionFactoryInterface */
    private $orderItemOptionFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var OrderItemOptionUpdaterInterface */
    private $orderItemUpdater;

    /** @var OrderItemInterface[] */
    private $factoryObjects = [];

    public function setup(): void
    {
        $this->customerOptionRepository = $this->createMock(CustomerOptionRepositoryInterface::class);
        $this->customerOptionRepository->method('findOneByCode')->withAnyParameters()->willReturn(
            $this->createMock(CustomerOptionInterface::class)
        );

        // OrderItemOptionFactory
        $this->orderItemOptionFactory = $this->createMock(OrderItemOptionFactoryInterface::class);
        $this->orderItemOptionFactory->method('createNew')->willReturnCallback(
            function ($customerOption, $value) {
                return array_shift($this->factoryObjects);
            }
        );

        // Mocking the entity manager
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->orderItemUpdater = new OrderItemOptionUpdater(
            $this->customerOptionRepository,
            $this->orderItemOptionFactory,
            $this->entityManager
        );
    }

    private function addFactoriedOrderItem(): OrderItemOptionInterface
    {
        $producedOption         = $this->createMock(OrderItemOptionInterface::class);
        $this->factoryObjects[] = $producedOption;

        return $producedOption;
    }

    public function testUpdateOrderItemOptionsWithNewConfig(): void
    {
        ### PREPARE
        $producedOption = $this->addFactoriedOrderItem();

        $orderItem = $this->createConfiguredMock(
            OrderItemInterface::class,
            ['getCustomerOptionConfiguration' => []]
        );

        $orderItem->expects($this->once())
            ->method('setCustomerOptionConfiguration')
            ->with($this->equalTo([$producedOption]));

        $this->entityManager->expects($this->once())->method('persist')->with($this->equalTo($producedOption));

        ### EXECUTE
        $this->orderItemUpdater->updateOrderItemOptions($orderItem, ['some_custom_option' => 'hello']);
    }

    public function testUpdateOrderItemOptionsWithCustomerOptionValue(): void
    {
        ### PREPARE
        $expectedValue = $this->createMock(CustomerOptionValueInterface::class);

        $producedOption = $this->addFactoriedOrderItem();
        $producedOption->expects($this->once())->method('setCustomerOptionValue')->with($expectedValue);
        $producedOption->expects($this->never())->method('setOptionValue');

        $orderItem = $this->createConfiguredMock(
            OrderItemInterface::class,
            ['getCustomerOptionConfiguration' => ['some_custom_option' => $producedOption]]
        );
        $orderItem->expects($this->never())->method('setCustomerOptionConfiguration');

        $this->entityManager->expects($this->never())->method('persist')->withAnyParameters();

        ### EXECUTE
        $this->orderItemUpdater->updateOrderItemOptions($orderItem, ['some_custom_option' => $expectedValue]);
    }

    public function testUpdateOrderItemOptionsWithValue(): void
    {
        ### PREPARE
        $expectedValue = 'some_beep';

        /** @var MockObject $producedOption */
        $producedOption = $this->addFactoriedOrderItem();
        $producedOption->expects($this->once())->method('setOptionValue')->with($this->equalTo($expectedValue));
        $producedOption->expects($this->never())->method('setCustomerOptionValue');

        $orderItem = $this->createConfiguredMock(
            OrderItemInterface::class,
            ['getCustomerOptionConfiguration' => ['some_custom_option' => $producedOption]]
        );
        $orderItem->expects($this->never())->method('setCustomerOptionConfiguration');

        $this->entityManager->expects($this->never())->method('persist')->withAnyParameters();

        ### EXECUTE
        $this->orderItemUpdater->updateOrderItemOptions($orderItem, ['some_custom_option' => $expectedValue]);
    }
}
