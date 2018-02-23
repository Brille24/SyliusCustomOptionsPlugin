<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 21.02.18
 * Time: 12:36
 */

namespace Brille24\CustomerOptionsPlugin\EventListener;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerOptionValueListener
{
    /** @var EntityRepository */
    private $channelRepository;

    /**
     * CustomerOptionValueListener constructor.
     *
     * @param EntityRepository $channelRepository
     */
    public function __construct(
        EntityRepository $channelRepository
    )
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof CustomerOptionValueInterface) {
            $this->addChannelPricesToNewValue($entity);
        }
    }

    private function addChannelPricesToNewValue(CustomerOptionValueInterface $value){
        $prices = $value->getPrices();

        $existingChannels = [];
        foreach ($prices as $price) {
            $existingChannels[] = $price->getChannel();
        }

        $channels = $this->channelRepository->findAll();

        foreach ($channels as $channel) {
            if (!in_array($channel, $existingChannels)) {
                $newPrice = new CustomerOptionValuePrice();
                $newPrice->setChannel($channel);
                $value->addPrice($newPrice);
            }
        }
    }
}
