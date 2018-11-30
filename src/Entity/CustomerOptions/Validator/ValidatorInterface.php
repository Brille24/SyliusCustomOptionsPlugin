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

    /**
     * @param ConditionInterface $condition
     */
    public function addCondition(ConditionInterface $condition): void;

    /**
     * @param ConditionInterface $condition
     */
    public function removeCondition(ConditionInterface $condition): void;

    /**
     * @return ConditionInterface[]
     */
    public function getConstraints(): array;

    /**
     * @param ConditionInterface[]|null $constraints
     */
    public function setConstraints(?array $constraints): void;

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
     * @return ErrorMessageInterface
     */
    public function getErrorMessage(): ?ErrorMessageInterface;

    /**
     * @param ErrorMessageInterface $message
     */
    public function setErrorMessage(?ErrorMessageInterface $message): void;
}
