<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:55
 */

namespace Brille24\CustomerOptionsBundle\Repository;


use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $code
     * @return null|CustomerOptionInterface
     */
    public function findOneByCode(string $code) : ?CustomerOptionInterface;

    /**
     * @param string $type
     * @return array
     */
    public function findByType(string $type) : array;

    /**
     * @param bool $required
     * @return array
     */
    public function findByRequired(bool $required) : array;
}