<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Repository;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $code
     *
     * @return CustomerOptionGroupInterface
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCode(string $code): CustomerOptionGroupInterface;
}
