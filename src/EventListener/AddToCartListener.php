<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class AddToCartListener
{
    private RequestStack $requestStack;

    private EntityManagerInterface $entityManager;

    private OrderItemOptionFactoryInterface $orderItemOptionFactory;

    private OrderProcessorInterface $orderProcessor;

    private CustomerOptionRepositoryInterface $customerOptionRepository;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        OrderItemOptionFactoryInterface $itemOptionFactory,
        OrderProcessorInterface $orderProcessor,
        CustomerOptionRepositoryInterface $customerOptionRepository,
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->orderItemOptionFactory = $itemOptionFactory;
        $this->orderProcessor = $orderProcessor;
        $this->customerOptionRepository = $customerOptionRepository;
    }

    public function addItemToCart(ResourceControllerEvent $event): void
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        // If the order is null, it's an old order item with an existing reference in the database
        if ($orderItem->getOrder() === null) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return;
        }

        $customerOptionConfiguration = $this->getCustomerOptionsFromRequest($request);

        $salesOrderConfigurations = [];
        foreach ($customerOptionConfiguration as $customerOptionCode => $valueArray) {
            if (!is_array($valueArray)) {
                $valueArray = [$valueArray];
            }

            foreach ($valueArray as $key => $value) {
                if (is_array($value)) {
                    $valueArray = array_merge($valueArray, $value);
                    unset($valueArray[$key]);
                }
            }

            foreach ($valueArray as $value) {
                // Creating the item
                $salesOrderConfiguration = $this->orderItemOptionFactory->createNewFromStrings(
                    $orderItem,
                    $customerOptionCode,
                    $value,
                );

                $this->entityManager->persist($salesOrderConfiguration);

                $salesOrderConfigurations[] = $salesOrderConfiguration;
            }
        }

        $orderItem->setCustomerOptionConfiguration($salesOrderConfigurations);
        /** @var OrderInterface $order */
        $order = $orderItem->getOrder();
        $this->orderProcessor->process($order);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();
    }

    /**
     * Gets the customer options from the request
     */
    public function getCustomerOptionsFromRequest(Request $request): array
    {
        /** @var array $addToCart */
        $addToCart = $request->request->all('sylius_add_to_cart');

        if (!isset($addToCart['customer_options'])) {
            return [];
        }

        // Date options need a little extra attention
        // We transform the date fields into a single date string
        foreach ($addToCart['customer_options'] as $code => $value) {
            $customerOption = $this->customerOptionRepository->findOneByCode($code);
            Assert::notNull($customerOption);

            switch ($customerOption->getType()) {
                case CustomerOptionTypeEnum::DATE:
                    $day = $value['day'];
                    $month = $value['month'];
                    $year = $value['year'];
                    $addToCart['customer_options'][$code] = sprintf('%d-%d-%d', $year, $month, $day);

                    break;
                case CustomerOptionTypeEnum::DATETIME:
                    $date = $value['date'];
                    $time = $value['time'];
                    $day = $date['day'];
                    $month = $date['month'];
                    $year = $date['year'];

                    $hour = $time['hour'] ?? 0;
                    $minute = $time['minute'] ?? 0;

                    $addToCart['customer_options'][$code] = sprintf('%d-%d-%d %d:%d', $year, $month, $day, $hour, $minute);

                    break;
            }
        }

        return $addToCart['customer_options'];
    }
}
