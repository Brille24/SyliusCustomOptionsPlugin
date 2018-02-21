<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:55
 */

namespace Brille24\CustomerOptionsPlugin\Repository;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
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
