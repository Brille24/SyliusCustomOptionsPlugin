# UPGRADE FROM 1.6 TO 2.0
Adjustments are a way of changing the price in Sylius without changing the base price of the product. This means that with this change you can differenciate between the base price of the product and the additional charges that get added by customer options. The item total is still the base price of the product (unit * quantity) but on any item you can get the adjustments with `$item->getAdjustmentsTotalRecursively('CUSTOMER_OPTION_ADJUSTMENT')`. This makes it easier to add them to the views in the templates.

## What you need to change?
* If you are using the item totals for any calculation this should now also take the adjustment into accounts.
* With the new feature there are a lot of template changes, check which ones you need to copy and adapt again.
