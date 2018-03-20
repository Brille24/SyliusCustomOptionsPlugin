<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ValidatorInterface extends ResourceInterface
{
    /**
     * @return Collection
     */
    public function getConditions(): ?Collection;

    /**
     * @param Collection $conditions
     */
    public function setConditions(?Collection $conditions): void;

    /**
     * @param ConditionInterface $condition
     */
    public function addCondition(ConditionInterface $condition): void;

    /**
     * @param ConditionInterface $condition
     */
    public function removeCondition(ConditionInterface $condition): void;

    /**
     * @return Collection
     */
    public function getConstraints(): ?Collection;

    /**
     * @param Collection $constraints
     */
    public function setConstraints(?Collection $constraints): void;

    /**
     * @param ConditionInterface $constraint
     */
    public function addConstraint(ConditionInterface $constraint): void;

    /**
     * @param ConditionInterface $constraint
     */
    public function removeConstraint(ConditionInterface $constraint): void;

    /**
     * @return CustomerOptionGroupInterface
     */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface;

    /**
     * @param CustomerOptionGroupInterface $customerOptionGroup
     */
    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void;

    /**
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * @param string $message
     */
    public function setErrorMessage(string $message): void;
}