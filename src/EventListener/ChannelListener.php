<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 23.02.18
 * Time: 15:09
 */

namespace Brille24\CustomerOptionsPlugin\EventListener;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelListener
{
    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();

        if($entity instanceof ChannelInterface){
            $this->addNewChannelToAllValues($entity, $args->getEntityManager());
        }
    }

    private function addNewChannelToAllValues(ChannelInterface $channel, EntityManagerInterface $em){
        /** @var CustomerOptionValueInterface[] $values */
        $values = $em->getRepository(CustomerOptionValue::class)->findAll();

        foreach ($values as $value){
            $existingChannels = [];
            foreach ($value->getPrices() as $price){
                $existingChannels[] = $price->getChannel();
            }

            if(!in_array($channel, $existingChannels)) {
                $newPrice = new CustomerOptionValuePrice();
                $newPrice->setChannel($channel);
                $value->addPrice($newPrice);
                $em->persist($value);
            }
        }
    }
}