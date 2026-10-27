<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ValidatorInterface extends ResourceInterface
{
    /**
     * @return ConditionInterface[]
     */
    public function getConditions(): array;

    /**
     * @param ConditionInterface[]|null $conditions
     */
    public function setConditions(?array $conditions): void;

    public function addCondition(ConditionInterface $condition): void;

    public function removeCondition(ConditionInterface $condition): void;

    /**
     * @return ConstraintInterface[]
     */
    public function getConstraints(): array;

    /**
     * @param ConstraintInterface[]|null $constraints
     */
    public function setConstraints(?array $constraints): void;

    public function addConstraint(ConstraintInterface $constraint): void;

    public function removeConstraint(ConstraintInterface $constraint): void;

    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface;

    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void;

    public function getErrorMessage(): ?ErrorMessageInterface;

    public function setErrorMessage(?ErrorMessageInterface $message): void;
}
