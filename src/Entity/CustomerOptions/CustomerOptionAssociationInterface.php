<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 15:57
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionAssociationInterface extends ResourceInterface
{
    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @param int $position
     */
    public function setPosition(int $position): void;

    /**
     * @return CustomerOptionGroupInterface
     */
    public function getGroup(): ?CustomerOptionGroupInterface;

    /**
     * @param CustomerOptionGroupInterface $group
     */
    public function setGroup(CustomerOptionGroupInterface $group): void;

    /**
     * @return CustomerOptionInterface
     */
    public function getOption(): ?CustomerOptionInterface;

    /**
     * @param CustomerOptionInterface $option
     */
    public function setOption(CustomerOptionInterface $option): void;
}
