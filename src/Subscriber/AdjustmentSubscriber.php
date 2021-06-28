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

namespace Brille24\SyliusCustomerOptionsPlugin\Subscriber;

use Brille24\SyliusCustomerOptionsPlugin\Event\OrderItemOptionEvent;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


final class AdjustmentSubscriber implements EventSubscriberInterface
{
    public const CUSTOMER_OPTION_ADJUSTMENT = 'customer_option';

    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;

    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            CustomerOptionRecalculator::EVENT_PREFIX_ORDER_ITEM_OPTION_TYPE.'select' => 'addSelectAdjustment',
            CustomerOptionRecalculator::EVENT_PREFIX_ORDER_ITEM_OPTION_TYPE.'multi_select' => 'addSelectAdjustment',
        );
    }

    public function addSelectAdjustment(OrderItemOptionEvent $event): void
    {
        $orderItemOption = $event->getOrderItemOption();

        // Skip all customer options that don't have customer option values as they can not have a price like
        // text options
        if (null === $orderItemOption->getCustomerOptionValue()) {
            return;
        }

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
