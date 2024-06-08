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

namespace Brille24\SyliusCustomerOptionsPlugin\Repository;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionValueRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCode(string $code): ?CustomerOptionValueInterface;

    /**
    * Returns a list of values that does not have a price for the provided channel.
    *
    * @return array<CustomerOptionValueInterface>
    */
    public function findValuesWithoutPricingInChannel(ChannelInterface $channel): array;
}
