<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 12:26
 */

namespace Brille24\CustomerOptionsBundle\Entity;


class CustomerOptionCustomerOptionGroup implements CustomerOptionCustomerOptionGroupInterface
{
    /** @var int */
    protected $id;

    /** @var CustomerOptionInterface */
    protected $option;

    /** @var CustomerOptionGroupInterface */
    protected $group;

    /** @var int */
    protected $position;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->option;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOption(CustomerOptionInterface $option): void
    {
        $this->option = $option;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOptionGroup(CustomerOptionGroupInterface $group): void
    {
        $this->group = $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): ?int
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
}