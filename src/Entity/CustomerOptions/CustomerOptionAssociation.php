<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 15:55
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


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
        $this->position = 0;
    }

    /**
     * @return int|mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return CustomerOptionGroupInterface
     */
    public function getGroup(): ?CustomerOptionGroupInterface
    {
        return $this->group;
    }

    /**
     * @param CustomerOptionGroupInterface $group
     */
    public function setGroup(CustomerOptionGroupInterface $group): void
    {
        $this->group = $group;
    }

    /**
     * @return CustomerOptionInterface
     */
    public function getOption(): ?CustomerOptionInterface
    {
        return $this->option;
    }

    /**
     * @param CustomerOptionInterface $option
     */
    public function setOption(CustomerOptionInterface $option): void
    {
        $this->option = $option;
    }

}