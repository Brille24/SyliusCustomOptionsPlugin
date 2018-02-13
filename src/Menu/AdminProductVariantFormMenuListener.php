<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Menu;


use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminProductVariantFormMenuListener
{

    public function __construct() { }

    public function addItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        
        $menu->addChild('customer_options')
            ->setAttribute('template', '@Brille24CustomerOptionsPlugin/Product/Tab/_customer_options.html.twig')
            ->setLabel('brille24.ui.customer_options')
            ->setLabelAttribute('icon', 'setting');
}
}