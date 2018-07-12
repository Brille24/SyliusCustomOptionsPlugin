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

namespace Brille24\SyliusCustomerOptionsPlugin\Event;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class CustomerOptionMenuBuilderEvent extends MenuBuilderEvent
{
    /** @var CustomerOptionInterface */
    private $customerOption;

    public function __construct(FactoryInterface $factory, ItemInterface $menu, CustomerOptionInterface $customerOption)
    {
        parent::__construct($factory, $menu);

        $this->customerOption = $customerOption;
    }

    public function getCustomerOption(): CustomerOptionInterface
    {
        return $this->customerOption;
    }
}
