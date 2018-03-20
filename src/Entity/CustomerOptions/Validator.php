<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Validator implements ValidatorInterface
{
    const DEFAULT_ERROR_MESSAGE = 'One or more constraints failed.';

    /** @var int */
    protected $id;

    /** @var Collection */
    protected $conditions;

    /** @var Collection */
    protected $constraints;

    /** @var CustomerOptionGroupInterface */
    protected $customerOptionGroup;

    /** @var string */
    protected $errorMessage;

    public function __construct()
    {
        $this->constraints = new ArrayCollection();
        $this->conditions = new ArrayCollection();

        $this->errorMessage = self::DEFAULT_ERROR_MESSAGE;
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getConditions(): ?Collection
    {
        return $this->conditions;
    }

    /** {@inheritdoc} */
    public function setConditions(?Collection $conditions): void
    {
        foreach ($conditions as $condition){
            $condition->setValidator($this);
        }

        $this->conditions = $conditions;
    }

    /** {@inheritdoc} */
    public function addCondition(ConditionInterface $condition): void
    {
        $condition->setValidator($this);

        $this->conditions->add($condition);
    }

    /** {@inheritdoc} */
    public function removeCondition(ConditionInterface $condition): void
    {
        $condition->setValidator(null);

        $this->conditions->removeElement($condition);
    }

    /** {@inheritdoc} */
    public function getConstraints(): ?Collection
    {
        return $this->constraints;
    }

    /** {@inheritdoc} */
    public function setConstraints(?Collection $constraints): void
    {
        foreach ($constraints as $constraint){
            $constraint->setValidator($this);
        }

        $this->constraints = $constraints;
    }

    /** {@inheritdoc} */
    public function addConstraint(ConditionInterface $constraint): void
    {
        $constraint->setValidator($this);

        $this->constraints->add($constraint);
    }

    /** {@inheritdoc} */
    public function removeConstraint(ConditionInterface $constraint): void
    {
        $constraint->setValidator(null);

        $this->constraints->removeElement($constraint);
    }

    /** {@inheritdoc} */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface
    {
        return $this->customerOptionGroup;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void
    {
        $this->customerOptionGroup = $customerOptionGroup;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? self::DEFAULT_ERROR_MESSAGE;
    }

    public function setErrorMessage(?string $message): void
    {
        $this->errorMessage = $message ?? self::DEFAULT_ERROR_MESSAGE;
    }
}