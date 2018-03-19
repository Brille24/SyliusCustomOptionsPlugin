<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Sylius\Component\Resource\Model\ResourceInterface;

interface ConditionInterface extends ResourceInterface
{
    /**
     * @return CustomerOptionInterface
     */
    public function getCustomerOption(): CustomerOptionInterface;

    /**
     * @param CustomerOptionInterface $customerOption
     */
    public function setCustomerOption(CustomerOptionInterface $customerOption): void;

    /**
     * @return string
     */
    public function getComparator(): string ;

    /**
     * @param string $comparator
     */
    public function setComparator(string $comparator): void;

    /**
     * @return int
     */
    public function getValue(): int;

    /**
     * @param int $value
     */
    public function setValue(int $value): void;
}