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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerOptionValueRepository extends EntityRepository implements CustomerOptionValueRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findOneByCode(string $code): ?CustomerOptionValueInterface
    {
        return $this->createQueryBuilder('v')
            ->where('v.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritdoc
     */
    public function findValuesWithoutPricingInChannel(ChannelInterface $channel): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.prices', 'p')
            ->where('p.channel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;
    }
}
