<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 15:42
 */

namespace Brille24\CustomerOptionsPlugin\Menu;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Event\CustomerOptionMenuBuilderEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CustomerOptionFormMenuBuilder
{
    public const EVENT_NAME = 'brille24.menu.admin.customer_option.form';

    private $factory;

    private $eventDispatcher;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $dispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $dispatcher;
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('customer_option', $options) || !$options['customer_option'] instanceof CustomerOptionInterface) {
            return $menu;
        }

        $menu
            ->addChild('details')
            ->setAttribute('template', '@Brille24CustomerOptionsPlugin/CustomerOption/Tab/_details.html.twig')
            ->setLabel('brille24.ui.details')
            ->setCurrent(true)
        ;

        $menu
            ->addChild('pricing')
            ->setAttribute('template', '@Brille24CustomerOptionsPlugin/CustomerOption/Tab/_pricing.html.twig')
            ->setLabel('brille24.ui.pricing')
        ;

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new CustomerOptionMenuBuilderEvent($this->factory, $menu, $options['customer_option'])
        );

        return $menu;
    }
}
