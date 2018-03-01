<?php

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
