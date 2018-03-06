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

namespace Brille24\CustomerOptionsPlugin\Repository;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class CustomerOptionGroupRepository extends EntityRepository implements CustomerOptionGroupRepositoryInterface
{
    /**
     * @param string $code
     *
     * @return CustomerOptionGroupInterface
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCode(string $code): CustomerOptionGroupInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
