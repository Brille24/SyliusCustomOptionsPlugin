<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;

final class OrderItemOptionUpdater implements OrderItemOptionUpdaterInterface
{
    /**
     * @var CustomerOptionRepositoryInterface
     */
    private $customerOptionRepository;

    /**
     * @var OrderItemOptionFactoryInterface
     */
    private $orderItemOptionFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        OrderItemOptionFactoryInterface $orderItemOptionFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->customerOptionRepository = $customerOptionRepository;
        $this->orderItemOptionFactory   = $orderItemOptionFactory;
        $this->entityManager            = $entityManager;
    }

    /** {@inheritdoc} */
    public function updateOrderItemOptions(OrderItemInterface $orderItem, array $data): void
    {
        $orderItemOptions = $orderItem->getCustomerOptionConfiguration(true);

        if (count($orderItemOptions) === 0) {
            $this->createNewOrderItemOptions($orderItem, $data);

            return;
        }

        foreach ($data as $customerOptionCode => $newValue) {
            $orderItemOption = $orderItemOptions[$customerOptionCode] ?? null;
            Assert::isInstanceOf($orderItemOption, OrderItemOptionInterface::class);

            if ($orderItemOption === null) {
                continue;
            }

            if ($newValue instanceof CustomerOptionValueInterface) {
                $orderItemOption->setCustomerOptionValue($newValue);
            } else {
                $orderItemOption->setOptionValue($newValue);
            }
        }

        $this->entityManager->flush();
    }

    private function createNewOrderItemOptions(OrderItemInterface $orderItem, array $data): void
    {
        $customerOptionConfiguration = [];

        foreach ($data as $customerOptionCode => $value) {
            $customerOption  = $this->customerOptionRepository->findOneByCode($customerOptionCode);
            $orderItemOption = $this->orderItemOptionFactory->createNew($customerOption, $value);

            $orderItemOption->setOrderItem($orderItem);

            $this->entityManager->persist($orderItemOption);
            $customerOptionConfiguration[] = $orderItemOption;
        }

        $orderItem->setCustomerOptionConfiguration($customerOptionConfiguration);

        $this->entityManager->flush();
    }
}
