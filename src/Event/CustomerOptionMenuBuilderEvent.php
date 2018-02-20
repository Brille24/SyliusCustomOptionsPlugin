<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 14.02.18
 * Time: 15:54
 */

namespace Brille24\CustomerOptionsPlugin\Event;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class CustomerOptionMenuBuilderEvent extends MenuBuilderEvent
{
    private $customerOption;

    public function __construct(FactoryInterface $factory, ItemInterface $menu, CustomerOptionInterface $customerOption)
    {
        parent::__construct($factory, $menu);

        $this->customerOption = $customerOption;
    }

    public function getCustomerOption(): CustomerOptionInterface{
        return $this->customerOption;
    }
}