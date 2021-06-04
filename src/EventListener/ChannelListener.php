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

namespace Brille24\SyliusCustomerOptionsPlugin\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelListener
{
    /** @var CustomerOptionValuePriceFactoryInterface */
    private $valuePriceFactory;

    /**
     * @param CustomerOptionValuePriceFactoryInterface $valuePriceFactory
     */
    public function __construct(
        CustomerOptionValuePriceFactoryInterface $valuePriceFactory
    ) {
        $this->valuePriceFactory = $valuePriceFactory;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof ChannelInterface) {
            $this->addNewChannelToAllValues($entity, $args->getEntityManager());
        }
    }

    private function addNewChannelToAllValues(ChannelInterface $channel, EntityManagerInterface $em): void
    {
        /** @var CustomerOptionValueInterface[] $values */
        $values = $em->getRepository(CustomerOptionValue::class)->findAll();

        foreach ($values as $value) {
            $existingChannels = [];
            foreach ($value->getPrices() as $price) {
                $existingChannels[] = $price->getChannel();
            }

            if (!in_array($channel, $existingChannels, true)) {
                $newPrice = $this->valuePriceFactory->createNew();
                $newPrice->setChannel($channel);
                $value->addPrice($newPrice);
                $em->persist($value);
            }
        }
    }
}
