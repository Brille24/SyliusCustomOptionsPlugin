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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Event\CustomerOptionMenuBuilderEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CustomerOptionFormMenuBuilder
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
            ->setAttribute('template', '@Brille24SyliusCustomerOptionsPlugin/CustomerOption/Tab/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true)
        ;

        $menu
            ->addChild('pricing')
            ->setAttribute('template', '@Brille24SyliusCustomerOptionsPlugin/CustomerOption/Tab/_pricing.html.twig')
            ->setLabel('sylius.ui.pricing')
        ;

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new CustomerOptionMenuBuilderEvent($this->factory, $menu, $options['customer_option'])
        );

        return $menu;
    }
}
