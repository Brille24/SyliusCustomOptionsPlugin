## Customized order adjustments for customer options

`Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator` handles all the adjustments for the custom options. You can add custom adjustment simply by creating a subscriber and listening to one of the CustomerOptionRecalculator events.

### Events 
The following events are dispatched in this order.

| Event name | Constant | Event class | description |
| ------------- | ------------- | ------------- | ------------- |
| brille24.customer_option_recalculator_event.pre.remove_adjustments        | EVENT_PRE_REMOVE_ADJUSTMENTS        | OrderEvent           | Before the customer option adjustments are removed |
| brille24.customer_option_recalculator_event.post.remove_adjustments       | EVENT_POST_REMOVE_ADJUSTMENTS       | OrderEvent           | After the customer option adjustments are removed |
| brille24.customer_option_recalculator_event.pre.order_item                | EVENT_PRE_ORDER_ITEM                | OrderItemEvent       | Dispatched at the start for each OrderItem |
| brille24.customer_option_recalculator_event.order_item_option             | EVENT_ORDER_ITEM_OPTION             | OrderItemOptionEvent | Dispatched for each order item option |
| brille24.customer_option_recalculator_event.order_item_option.type.[TYPE] | EVENT_PREFIX_ORDER_ITEM_OPTION_TYPE | OrderItemOptionEvent | Should be used if you would like to listen to a specific type |
| brille24.customer_option_recalculator_event.order_item_option.code.[CODE] | EVENT_PREFIX_ORDER_ITEM_OPTION_CODE | OrderItemOptionEvent | Should be used to customize specific options |
| brille24.customer_option_recalculator_event.post.order_item               | EVENT_POST_ORDER_ITEM               | OrderItemEvent       | Dispatched at the start for each OrderItem |

### Creating a custom adjustment 
Let's say our products have a text field for a gift card. The gift card costs 1 cent for each 10 words. First you have to add a CustomerOption with the code "gift_card" and type "text". After you added the option to your products you can create a subscriber to generate adjustments when customers insert a text for the gift card. Since this is a specific CustomerOption we want to subscribe to the `brille24.customer_option_recalculator_event.order_item_option.code.[CODE]` event.

```php
<?php
declare(strict_types=1);

namespace App\Module\Brille24CustomerOptionsPlugin\Subscriber;

use Brille24\SyliusCustomerOptionsPlugin\Event\OrderItemOptionEvent;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GiftCardAdjustmentSubscriber implements EventSubscriberInterface
{
    /** @var AdjustmentFactoryInterface  */
    private $adjustmentFactory;

    public function __construct(
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            CustomerOptionRecalculator::EVENT_PREFIX_ORDER_ITEM_OPTION_CODE.'gift_card' => 'createAdjustment',
        );
    }

    public function createAdjustment(
        OrderItemOptionEvent $event
    ) {
        $orderItemOption = $event->getOrderItemOption();
        $orderItem = $orderItemOption->getOrderItem();

        $textValue = trim($orderItemOption->getOptionValue());
        $unitPrice = (int) ceil(mb_strlen($textValue) / 10);

        if (mb_strlen($textValue) > 0) {
            foreach ($orderItem->getUnits() as $unit) {
                $adjustment = $this->adjustmentFactory->createWithData(
                    CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT,
                    'Gift card',
                    $unitPrice,
                    false,
                    []
                );
    
                $unit->addAdjustment($adjustment);
            }
        }
    }
}

```

### Remove your a custom adjustment 
If you used the `CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT` constant for your adjustments you are done! All adjustments of that type will be removed when the CustomerOptionRecalculator is executed. If you your own type for the adjustments you can use either the `brille24.customer_option_recalculator_event.pre.remove_adjustments` or `brille24.customer_option_recalculator_event.post.remove_adjustments` events to remove your adjustments.

```php
<?php
declare(strict_types=1);

namespace App\Module\Brille24CustomerOptionsPlugin\Subscriber;

use Brille24\SyliusCustomerOptionsPlugin\Event\OrderEvent;
use Brille24\SyliusCustomerOptionsPlugin\Event\OrderItemOptionEvent;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class GiftCardAdjustmentSubscriber implements EventSubscriberInterface
{
    const GIFT_CARD_ADJUSTMENT_TYPE = 'gift_card';
    
    /** @var AdjustmentFactoryInterface  */
    private $adjustmentFactory;

    /**
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(
        AdjustmentFactoryInterface $adjustmentFactory
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            CustomerOptionRecalculator::EVENT_POST_REMOVE_ADJUSTMENTS => 'removeAdjustment',
            CustomerOptionRecalculator::EVENT_PREFIX_ORDER_ITEM_OPTION_CODE.'gift_card' => 'createAdjustment',
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function removeAdjustment(
        OrderEvent $event
    ) {
        $order = $event->getOrder();
        $order->removeAdjustmentsRecursively(self::GIFT_CARD_ADJUSTMENT_TYPE);
    }

    /**
     * @param OrderItemOptionEvent $event
     */
    public function createAdjustment(
        OrderItemOptionEvent $event
    ) {
        $orderItemOption = $event->getOrderItemOption();
        $orderItem = $orderItemOption->getOrderItem();

        $textValue = trim($orderItemOption->getOptionValue());
        $unitPrice = (int) ceil(mb_strlen($textValue) / 10);

        if (mb_strlen($textValue) > 0) {
            foreach ($orderItem->getUnits() as $unit) {
                $adjustment = $this->adjustmentFactory->createWithData(
                    self::GIFT_CARD_ADJUSTMENT_TYPE,
                    'Gift card',
                    $unitPrice,
                    false,
                    []
                );

                $unit->addAdjustment($adjustment);
            }
        }
    }
}
```