<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
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
        // Get the possible customer options
        /** @var ProductInterface $product */
        $product            = $orderItem->getProduct();
        $customerOptions    = $product->getCustomerOptions();

        $customerOptionConfiguration = $orderItem->getCustomerOptionConfiguration();

        // For each possible customer option
        foreach ($customerOptions as $customerOption) {
            $customerOptionCode = $customerOption->getCode();

            // If the order item doesn't have this option already and the option is provided in the data
            if (array_key_exists($customerOptionCode, $data) &&
                !array_key_exists($customerOptionCode, $customerOptionConfiguration)
            ) {
                // Add that option to the order item
                $orderItemOption = $this->orderItemOptionFactory->createNew($customerOption, $data[$customerOptionCode]);

                $orderItemOption->setOrderItem($orderItem);

                $this->entityManager->persist($orderItemOption);
                $customerOptionConfiguration[] = $orderItemOption;
            }
        }

        // Update the order items option config
        $orderItem->setCustomerOptionConfiguration($customerOptionConfiguration);
    }
}
