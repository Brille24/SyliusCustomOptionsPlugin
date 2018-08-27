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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity;

use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface
{
    /**
     * @param OrderItemOptionInterface[] $customerOptionConfiguration
     */
    public function setCustomerOptionConfiguration(array $customerOptionConfiguration): void;

    /**
     * @return OrderItemOptionInterface[]
     */
    public function getCustomerOptionConfiguration(): array;

    /**
     * Returns the compressed version of the customer option configuration as though it was sent through a web-request.
     *
     * @return array
     */
    public function getCustomerOptionConfigurationAsSimpleArray(): array;
}
