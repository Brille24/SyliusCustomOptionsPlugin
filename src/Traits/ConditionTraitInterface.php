<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Traits;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;

interface ConditionTraitInterface
{
    /**
     * @return CustomerOptionInterface
     */
    public function getCustomerOption(): ?CustomerOptionInterface;

    /**
     * @param CustomerOptionInterface $customerOption
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    /**
     * @return string
     */
    public function getComparator(): ?string;

    /**
     * @param string $comparator
     */
    public function setComparator(?string $comparator): void;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value): void;

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ?ValidatorInterface;

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(?ValidatorInterface $validator): void;

    /**
     * @param $value
     * @param string $optionType
     *
     * @return bool
     */
    public function isMet($value, ?string $optionType = null): bool;
}
