<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Repository;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class CustomerOptionValueRepository extends EntityRepository implements CustomerOptionValueRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByCode(string $code): ?CustomerOptionValueInterface
    {
        return $this->createQueryBuilder('v')
            ->where('v.codeh = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
