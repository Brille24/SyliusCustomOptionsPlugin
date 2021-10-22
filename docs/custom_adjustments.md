## Customizing Order Adjustment Calculations

`Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator` handles all the adjustments for the customer
options. You can add custom adjustment simply by creating a subscriber and listening to one of the
`CustomerOptionRecalculator` events.

### Events

The following events are dispatched in this order.

| Event class                           | description                                                                         |
| ------------------------------------- | ----------------------------------------------------------------------------------- |
| `RemoveCustomerOptionFromOrderEvent`  | This gets called to give the opportunity to remove more customer option adjustments |
| `RecalculateOrderItemOptionEvent`     | Gets called for every order item option that was added for the order                |

### Creating a custom adjustment

Let's say our products have a text field for a gift card. The gift card costs 1 cent every 10 characters. First you have
to add a `CustomerOption` with the code "gift_card" and type "text". After you added the option to your products you can
create a subscriber to generate adjustments when customers insert a text for the gift card.

```php
<?php
declare(strict_types=1);

namespace App\Module\Brille24CustomerOptionsPlugin\Subscriber;

use Brille24\SyliusCustomerOptionsPlugin\Event\RecalculateOrderItemOptionEvent;
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

    public static function getSubscribedEvents(): array
    {
        return [
            RecalculateOrderItemOptionEvent::class => 'createAdjustment',
        ];
    }

    public function createAdjustment(
        RecalculateOrderItemOptionEvent $event
    ) {
        $orderItemOption = $event->getOrderItemOption();
        // Skip handling all other customer options
        if ($orderItemOption->getCustomerOptionCode() !== 'gift_card') {
            return;
        }

        $orderItem = $orderItemOption->getOrderItem();

        $textValue = trim($orderItemOption->getOptionValue());
        $costForTextOnCard = (int) ceil(mb_strlen($textValue) / 10);

        if (mb_strlen($textValue) > 0) {
            foreach ($orderItem->getUnits() as $unit) {
                $adjustment = $this->adjustmentFactory->createWithData(
                    CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT,
                    'Gift card',
                    $costForTextOnCard,
                    false,
                    []
                );
    
                $unit->addAdjustment($adjustment);
            }
        }
    }
}

```

### Remove your custom adjustment

If you used the `CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT` constant for your adjustments you are done! All
adjustments of that type will be removed when the `CustomerOptionRecalculator` is executed. If you want to use your own
type for the adjustments you can use the `RemoveCustomerOptionFromOrderEvent` to remove your adjustments.

```php
<?php
declare(strict_types=1);

namespace App\Module\Brille24CustomerOptionsPlugin\Subscriber;

use Brille24\SyliusCustomerOptionsPlugin\Event\RemoveCustomerOptionFromOrderEvent;
use Brille24\SyliusCustomerOptionsPlugin\Event\RecalculateOrderItemOptionEvent;
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
        return [
            RemoveCustomerOptionFromOrderEvent::class => 'removeAdjustment',
            RecalculateOrderItemOptionEvent::class => 'createAdjustment',
        ];
    }

    /**
     * @param RemoveCustomerOptionFromOrderEvent $event
     */
    public function removeAdjustment(RemoveCustomerOptionFromOrderEvent $event)
    {
        $order = $event->getOrder();
        $order->removeAdjustmentsRecursively(self::GIFT_CARD_ADJUSTMENT_TYPE);
    }

    /**
     * @param RecalculateOrderItemOptionEvent $event
     */
    public function createAdjustment(RecalculateOrderItemOptionEvent $event)
    {
        $orderItemOption = $event->getOrderItemOption();
        // Skip handling all other customer options
        if ($orderItemOption->getCustomerOptionCode() !== 'gift_card') {
            return;
        }

        $orderItem = $orderItemOption->getOrderItem();

        $textValue = trim($orderItemOption->getOptionValue());
        $costForTextOnCard = (int) ceil(mb_strlen($textValue) / 10);

        if (mb_strlen($textValue) > 0) {
            foreach ($orderItem->getUnits() as $unit) {
                $adjustment = $this->adjustmentFactory->createWithData(
                    self::GIFT_CARD_ADJUSTMENT_TYPE,
                    'Gift card',
                    $costForTextOnCard,
                    false,
                    []
                );

                $unit->addAdjustment($adjustment);
            }
        }
    }
}
```
