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
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionRepositoryInterface extends RepositoryInterface
{
    public function findOneByCode(string $code): ?CustomerOptionInterface;

    public function findByName(string $name, string $locale): array;

    /**
     * @return CustomerOptionInterface[]
     */
    public function findByType(string $type): array;

    /**
     * @return CustomerOptionInterface[]
     */
    public function findByRequired(bool $required): array;
}
