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

/**
 * Class CustomerOptionAssociation
 * This class is used as an association between the Customer Option Group and the customer option ordering them by
 * their position
 *
 * @see CustomerOption
 * @see CustomerOptionGroup
 */
class CustomerOptionAssociation implements CustomerOptionAssociationInterface
{
    /** @var int */
    private $id;

    /** @var int */
    private $position;

    /** @var CustomerOptionGroupInterface */
    private $group;

    /** @var CustomerOptionInterface */
    private $option;

    public function __construct(int $position = 0)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(): ?CustomerOptionGroupInterface
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(?CustomerOptionGroupInterface $group): void
    {
        $this->group = $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(): ?CustomerOptionInterface
    {
        return $this->option;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(?CustomerOptionInterface $option): void
    {
        $this->option = $option;
    }
}
