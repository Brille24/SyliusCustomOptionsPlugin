<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

class OrderItem extends BaseOrderItem implements OrderItemInterface
{
    /** @var Collection */
    protected $configuration;

    /** {@inheritdoc} */
    public function setCustomerOptionConfiguration(array $configuration): void
    {
        $this->configuration = new ArrayCollection($configuration);
    }

    /** {@inheritdoc} */
    public function getCustomerOptionConfiguration(): array
    {
        return $this->configuration->toArray();
    }
}
