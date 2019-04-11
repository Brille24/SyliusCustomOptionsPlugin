<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;

final class OrderItemOptionUpdater implements OrderItemOptionUpdaterInterface
{
    /**
     * @var OrderItemOptionFactoryInterface
     */
    private $orderItemOptionFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        OrderItemOptionFactoryInterface $orderItemOptionFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->orderItemOptionFactory   = $orderItemOptionFactory;
        $this->entityManager            = $entityManager;
    }

    /** {@inheritdoc} */
    public function updateOrderItemOptions(OrderItemInterface $orderItem, array $data): void
    {
        $this->createMissingOrderItemOptions($orderItem, $data);

        $orderItemOptions = $orderItem->getCustomerOptionConfiguration(true);

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

    private function createMissingOrderItemOptions(OrderItemInterface $orderItem, array $data): void
    {
        /** @var ProductInterface $product */
        $product = $orderItem->getProduct();

        $customerOptions = $product->getCustomerOptions();

        $customerOptionConfiguration = $orderItem->getCustomerOptionConfiguration();

        foreach ($customerOptions as $customerOption) {
            if (array_key_exists($customerOption->getCode(), $data) &&
                !array_key_exists($customerOption->getCode(), $customerOptionConfiguration)
            ) {
                $orderItemOption = $this->orderItemOptionFactory->createNew($customerOption, $data[$customerOption->getCode()]);

                $orderItemOption->setOrderItem($orderItem);

                $this->entityManager->persist($orderItemOption);
                $customerOptionConfiguration[] = $orderItemOption;
            }
        }

        $orderItem->setCustomerOptionConfiguration($customerOptionConfiguration);

        $this->entityManager->flush();
    }
}
