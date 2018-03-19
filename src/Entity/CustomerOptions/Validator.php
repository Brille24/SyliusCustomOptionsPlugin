<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Doctrine\Common\Collections\Collection;

class Validator implements ValidatorInterface
{
    /** @var int */
    protected $id;

    /** @var Collection */
    protected $conditions;

    /** @var Collection */
    protected $constraints;

    /** @var CustomerOptionGroupInterface */
    protected $customerOptionGroup;

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    /** {@inheritdoc} */
    public function setConditions(Collection $conditions): void
    {
        $this->conditions = $conditions;
    }

    /** {@inheritdoc} */
    public function addCondition(ConditionInterface $condition): void
    {
        $this->conditions->add($condition);
    }

    /** {@inheritdoc} */
    public function removeCondition(ConditionInterface $condition): void
    {
        $this->conditions->removeElement($condition);
    }

    /** {@inheritdoc} */
    public function getConstraints(): Collection
    {
        return $this->constraints;
    }

    /** {@inheritdoc} */
    public function setConstraints(Collection $constraints): void
    {
        $this->constraints = $constraints;
    }

    /** {@inheritdoc} */
    public function addConstraint(ConditionInterface $constraint): void
    {
        $this->constraints->add($constraint);
    }

    /** {@inheritdoc} */
    public function removeConstraint(ConditionInterface $constraint): void
    {
        $this->constraints->removeElement($constraint);
    }

    /** {@inheritdoc} */
    public function getCustomerOptionGroup(): CustomerOptionGroupInterface
    {
        return $this->customerOptionGroup;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionGroup(CustomerOptionGroupInterface $customerOptionGroup): void
    {
        $this->customerOptionGroup = $customerOptionGroup;
    }
}