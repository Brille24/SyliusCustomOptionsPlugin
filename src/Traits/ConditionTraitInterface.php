<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;

interface ConditionTraitInterface
{
    public function getCustomerOption(): ?CustomerOptionInterface;

    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    public function getComparator(): ?string;

    public function setComparator(?string $comparator): void;

    public function getValue(): ?array;

    public function setValue(mixed $value): void;

    public function getValidator(): ?ValidatorInterface;

    public function setValidator(?ValidatorInterface $validator): void;

    public function isMet(mixed $value, ?string $optionType = null): bool;
}
