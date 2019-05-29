<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

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
    /** @var CustomerOptionRepositoryInterface */
    private $customerOptionRepository;

    public function __construct(
        OrderItemOptionFactoryInterface $orderItemOptionFactory,
        EntityManagerInterface $entityManager,
        CustomerOptionRepositoryInterface $customerOptionRepository
    ) {
        $this->orderItemOptionFactory   = $orderItemOptionFactory;
        $this->entityManager            = $entityManager;
        $this->customerOptionRepository = $customerOptionRepository;
    }

    /** {@inheritdoc} */
    public function updateOrderItemOptions(OrderItemInterface $orderItem, array $data): void
    {
        $orderItemOptions = $orderItem->getCustomerOptionConfiguration(true);

        $newConfig = [];
        foreach ($data as $customerOptionCode => $newValue) {
            $orderItemOption = $orderItemOptions[$customerOptionCode] ?? null;
            $customerOption  = $this->customerOptionRepository->findOneByCode($customerOptionCode);

            if (CustomerOptionTypeEnum::FILE === $customerOption->getType()) {
                // @TODO: Find a way to handle file options
                continue;
            }

            // If the new value is null, remove the option
            if (null === $newValue) {
                if (null !== $orderItemOption) {
                    $this->entityManager->remove($orderItemOption);
                }

                continue;
            }

            // If the option is an array, it means the option is a multi select.
            // We have to remove the old values before we can add new ones.
            if (is_array($orderItemOption)) {
                foreach ($orderItemOption as $value) {
                    $this->entityManager->remove($value);
                }
            }

            // Make sure we have an OrderItemOption
            if (null === $orderItemOption) {
                $orderItemOption = $this->orderItemOptionFactory->createForOptionAndValue($customerOption, '');
                $orderItemOption->setOrderItem($orderItem);
            }

            // Select & Date options need to be handled differently
            switch ($customerOption->getType()) {
                case CustomerOptionTypeEnum::SELECT:
                    $orderItemOption->setCustomerOptionValue($newValue);
                    $newConfig[] = $orderItemOption;

                    break;
                case CustomerOptionTypeEnum::MULTI_SELECT:
                    // Create an option value for every selected value
                    foreach ($newValue as $value) {
                        $orderItemOption = $this->orderItemOptionFactory->createForOptionAndValue($customerOption, '');
                        $orderItemOption->setOrderItem($orderItem);
                        $orderItemOption->setCustomerOptionValue($value);
                        $newConfig[] = $orderItemOption;
                    }

                    break;
                case CustomerOptionTypeEnum::DATE:
                    $orderItemOption->setOptionValue($newValue->format('Y-m-d'));
                    $newConfig[] = $orderItemOption;

                    break;
                case CustomerOptionTypeEnum::DATETIME:
                    $orderItemOption->setOptionValue($newValue->format('Y-m-d h:i'));
                    $newConfig[] = $orderItemOption;

                    break;
                default:
                    // Every other option can just be casted
                    $orderItemOption->setOptionValue((string) $newValue);
                    $newConfig[] = $orderItemOption;
            }
        }

        $orderItem->setCustomerOptionConfiguration($newConfig);

        $this->entityManager->flush();
    }
}
