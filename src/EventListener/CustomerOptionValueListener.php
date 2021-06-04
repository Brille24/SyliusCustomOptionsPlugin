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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

final class CustomerOptionValueListener
{
    /** @var CustomerOptionValuePriceFactoryInterface */
    private $valuePriceFactory;

    /** @var EntityRepository */
    private $channelRepository;

    /**
     * @param CustomerOptionValuePriceFactoryInterface $valuePriceFactory
     * @param EntityRepository                         $channelRepository
     */
    public function __construct(
        CustomerOptionValuePriceFactoryInterface $valuePriceFactory,
        EntityRepository $channelRepository
    ) {
        $this->valuePriceFactory = $valuePriceFactory;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof CustomerOptionValueInterface) {
            $this->addChannelPricesToNewValue($entity);
        }
    }

    private function addChannelPricesToNewValue(CustomerOptionValueInterface $value): void
    {
        $prices = $value->getPrices();

        $existingChannels = [];
        foreach ($prices as $price) {
            $existingChannels[] = $price->getChannel();
        }

        $channels = $this->channelRepository->findAll();

        foreach ($channels as $channel) {
            if (!in_array($channel, $existingChannels, true)) {
                $newPrice = $this->valuePriceFactory->createNew();
                $newPrice->setChannel($channel);
                $value->addPrice($newPrice);
            }
        }
    }
}
