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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class CustomerOptionRepository extends EntityRepository implements CustomerOptionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByCode(string $code): ?CustomerOptionInterface
    {
        return $this->createQueryBuilder('o')
            ->where('o.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * {@inheritdoc}
     *
     * @return CustomerOptionInterface[]
     */
    public function findByName(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getArrayResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByRequired(bool $required): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.required = :required')
            ->setParameter('required', $required)
            ->getQuery()
            ->getArrayResult()
            ;
    }
}
