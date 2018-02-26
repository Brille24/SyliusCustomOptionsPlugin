<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Sylius\Component\Core\Model\ChannelInterface;

class OrderItemOptionFactory implements OrderItemOptionFactoryInterface
{
    /**
     * @var ChannelInterface
     */
    private $channel;

    public function __construct(ChannelInterface $channel)
    {
        $this->channel = $channel;
    }

    /** {@inheritdoc} */
    public function createNew(CustomerOptionInterface $customerOption, $customerOptionValue): OrderItemOptionInterface
    {
        return new OrderItemOption($this->channel, $customerOption, $customerOptionValue);
    }
}