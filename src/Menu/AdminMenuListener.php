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

namespace Brille24\SyliusCustomerOptionsPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $submenu = $menu->getChild('catalog');
        if ($submenu instanceof ItemInterface) {
            $submenu->addChild('customer_options', ['route' => 'brille24_admin_customer_option_index'])
                ->setLabel('sylius.menu.admin.main.catalog.customer_options')
                ->setLabelAttribute('icon', 'options');
        }

        $submenu = $menu->getChild('catalog');
        if ($submenu instanceof ItemInterface) {
            $submenu->addChild('customer_option_groups', ['route' => 'brille24_admin_customer_option_group_index'])
                ->setLabel('sylius.menu.admin.main.catalog.customer_option_groups')
                ->setLabelAttribute('icon', 'options');
        }
    }
}
