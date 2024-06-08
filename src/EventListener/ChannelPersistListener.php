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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelPersistListener
{
    public function __construct(
        private CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        private CustomerOptionValueRepositoryInterface $customerOptionValueRepository
    ) {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $channel = $args->getObject();

        if (!$channel instanceof ChannelInterface) {
            return;
        }

        $customerOptionValues = $this->customerOptionValueRepository->findValuesWithoutPricingInChannel($channel);

        foreach ($customerOptionValues as $customerOptionValue) {
            /** @var CustomerOptionValuePriceInterface $newPrice */
            $newPrice = $this->customerOptionValuePriceFactory->createNew();
            $newPrice->setChannel($channel);
            $customerOptionValue->addPrice($newPrice);
            $args->getObjectManager()->persist($customerOptionValue);
        }
    }
}
