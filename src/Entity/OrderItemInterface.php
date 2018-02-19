<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Sylius\Component\Core\Model\OrderItemInterface as BaseOrderItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface
{
	/**
	 * @param array $customerOptionConfiguration
	 */
	public function setCustomerOptionConfiguration(array $customerOptionConfiguration): void;

	/**
	 * @return array
	 */
	public function getCustomerOptionConfiguration(): array;

}