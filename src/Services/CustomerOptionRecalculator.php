<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Services;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItem;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


final class CustomerOptionRecalculator implements OrderProcessorInterface
{
    public const EVENT_PRE_REMOVE_ADJUSTMENTS       = 'brille24.customer_option_recalculator_event.pre.remove_adjustments';
    public const EVENT_POST_REMOVE_ADJUSTMENTS      = 'brille24.customer_option_recalculator_event.post.remove_adjustments';

    public const EVENT_PRE_ORDER_ITEM               = 'brille24.customer_option_recalculator_event.pre.order_item';
    public const EVENT_ORDER_ITEM_OPTION            = 'brille24.customer_option_recalculator_event.order_item_option';
    public const EVENT_PREFIX_ORDER_ITEM_OPTION_TYPE= 'brille24.customer_option_recalculator_event.order_item_option.type.';
    public const EVENT_PREFIX_ORDER_ITEM_OPTION_CODE= 'brille24.customer_option_recalculator_event.order_item_option.code.';
    public const EVENT_POST_ORDER_ITEM              = 'brille24.customer_option_recalculator_event.post.order_item';

    public const CUSTOMER_OPTION_ADJUSTMENT = 'customer_option';

    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(AdjustmentFactoryInterface $adjustmentFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->eventDispatcher   = $eventDispatcher;
    }

    public function process(OrderInterface $order): void
    {
        $this->eventDispatcher->dispatch(
            self::EVENT_PRE_REMOVE_ADJUSTMENTS,
            new GenericEvent($order)
        );

        $order->removeAdjustmentsRecursively(self::CUSTOMER_OPTION_ADJUSTMENT);

        $this->eventDispatcher->dispatch(
            self::EVENT_POST_REMOVE_ADJUSTMENTS,
            new GenericEvent($order)
        );

        /** @var OrderItem $orderItem */
        foreach ($order->getItems() as $orderItem) {

            if (!$orderItem instanceof OrderItemInterface) {
                continue;
            }

            $this->eventDispatcher->dispatch(
                self::EVENT_PRE_ORDER_ITEM,
                new GenericEvent($orderItem)
            );

            /** @var OrderItemOptionInterface[] $configuration */
            $configuration = $orderItem->getCustomerOptionConfiguration();

            foreach ($configuration as $orderItemOption) {

                $this->eventDispatcher->dispatch(
                    self::EVENT_ORDER_ITEM_OPTION,
                    new GenericEvent($orderItemOption)
                );
                $this->eventDispatcher->dispatch(
                    self::EVENT_PREFIX_ORDER_ITEM_OPTION_TYPE.$orderItemOption->getCustomerOptionType(),
                    new GenericEvent($orderItemOption)
                );
                $this->eventDispatcher->dispatch(
                    self::EVENT_PREFIX_ORDER_ITEM_OPTION_CODE.$orderItemOption->getCustomerOptionCode(),
                    new GenericEvent($orderItemOption)
                );

                // Skip all customer options that don't have customer option values as they can not have a price like
                // text options
                if (null === $orderItemOption->getCustomerOptionValue()) {
                    continue;
                }

                $this->addOrderItemAdjustment($orderItemOption);
            }

            $this->eventDispatcher->dispatch(
                self::EVENT_POST_ORDER_ITEM,
                new GenericEvent($orderItem)
            );
        }
    }

    private function addOrderItemAdjustment(OrderItemOptionInterface $orderItemOption): void
    {
        foreach ($orderItemOption->getOrderItem()->getUnits() as $unit) {
            $adjustment = $this->adjustmentFactory->createWithData(
                self::CUSTOMER_OPTION_ADJUSTMENT,
                $orderItemOption->getCustomerOptionName(),
                $orderItemOption->getCalculatedPrice($orderItemOption->getOrderItem()->getUnitPrice())
            );

            $unit->addAdjustment($adjustment);
        }
    }
}
