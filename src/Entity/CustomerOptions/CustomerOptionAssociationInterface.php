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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionAssociationInterface extends ResourceInterface
{
    public function getPosition(): int;

    public function setPosition(int $position): void;

    public function getGroup(): ?CustomerOptionGroupInterface;

    public function setGroup(?CustomerOptionGroupInterface $group): void;

    public function getOption(): ?CustomerOptionInterface;

    public function setOption(?CustomerOptionInterface $option): void;
}
