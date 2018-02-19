<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{
	protected $configuration = [];

	/** {@inheritdoc} */
	public function setCustomerOptionConfiguration(array $configuration): void
	{
		$this->configuration = $configuration;
	}

	/** {@inheritdoc} */
	public function getCustomerOptionConfiguration(): array
	{
		return $this->configuration;
	}
}