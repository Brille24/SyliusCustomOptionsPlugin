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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionGroupRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $code
     *
     * @return CustomerOptionGroupInterface|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCode(string $code): ?CustomerOptionGroupInterface;

    /**
     * @param string $name
     * @param string $locale
     *
     * @return CustomerOptionGroupInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
