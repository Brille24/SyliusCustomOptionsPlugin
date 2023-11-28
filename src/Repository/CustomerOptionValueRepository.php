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
     *
     * This creates a query to get all customerOptionValues in this channel and then selects the inverse
     */
    public function findValuesWithoutPricingInChannel(ChannelInterface $channel): array
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('v')
            ->where(
                $expr->notIn(
                    'v.id',
                    $this->createQueryBuilder('p')
                        ->select('p.id')
                        ->where('p.channel = :channel')
                        ->getDQL()
                )
            )
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;
    }
}
