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

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminProductVariantFormMenuListener
{
    public function __construct()
    {
    }

    public function addItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild('customer_options')
            ->setAttribute('template', '@Brille24SyliusCustomerOptionsPlugin/Product/Tab/_customer_options.html.twig')
            ->setLabel('brille24.ui.customer_options')
            ->setLabelAttribute('icon', 'setting');
    }
}
