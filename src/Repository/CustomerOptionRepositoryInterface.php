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
    /**
     * @param string $code
     *
     * @return CustomerOptionInterface|null
     */
    public function findOneByCode(string $code): ?CustomerOptionInterface;

    /**
     * @param string $name
     * @param string $locale
     *
     * @return array
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @param string $type
     *
     * @return CustomerOptionInterface[]
     */
    public function findByType(string $type): array;

    /**
     * @param bool $required
     *
     * @return CustomerOptionInterface[]
     */
    public function findByRequired(bool $required): array;
}
