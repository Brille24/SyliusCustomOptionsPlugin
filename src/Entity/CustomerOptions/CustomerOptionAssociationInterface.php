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
