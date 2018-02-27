<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Repository;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionValueRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $code
     *
     * @return CustomerOptionValueInterface|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCode(string $code): ?CustomerOptionValueInterface;
}
