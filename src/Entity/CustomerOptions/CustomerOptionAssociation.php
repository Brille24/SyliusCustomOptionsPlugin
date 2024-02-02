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

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CustomerOptionAssociation
 * This class is used as an association between the Customer Option Group and the customer option ordering them by
 * their position
 *
 * @see CustomerOption
 * @see CustomerOptionGroup
 */
#[ORM\Entity]
#[ORM\Table(name: 'brille24_customer_option_association')]
#[ORM\UniqueConstraint(name: 'option_group_unique', columns: ['option_id', 'group_id'])]
class CustomerOptionAssociation implements CustomerOptionAssociationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CustomerOptionGroupInterface::class, cascade: ['persist'], inversedBy: 'optionAssociations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?CustomerOptionGroupInterface $group = null;

    #[ORM\ManyToOne(targetEntity: CustomerOptionInterface::class, cascade: ['persist'], inversedBy: 'groupAssociations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?CustomerOptionInterface $option = null;

    #[ORM\Column(type: 'integer')]
    protected int $position = 0;

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return (string) $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @inheritdoc
     */
    public function getGroup(): ?CustomerOptionGroupInterface
    {
        return $this->group;
    }

    /**
     * @inheritdoc
     */
    public function setGroup(?CustomerOptionGroupInterface $group): void
    {
        $this->group = $group;
    }

    /**
     * @inheritdoc
     */
    public function getOption(): ?CustomerOptionInterface
    {
        return $this->option;
    }

    /**
     * @inheritdoc
     */
    public function setOption(?CustomerOptionInterface $option): void
    {
        $this->option = $option;
    }
}
