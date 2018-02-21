<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Traits;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;

/**
 * Trait CustomerOptionTrait
 *
 * This is a trait to attach customer options to an object
 *
 * @see     CustomerOptionGroupInterface
 */
trait CustomerOptionableTrait
{
    /** @var CustomerOptionGroupInterface|null */
    private $customerOptionGroup;

    public function __construct()
    {
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

    /** {@inheritdoc} */
    public function getCustomerOptions(): array
    {
        if (null === $this->customerOptionGroup) {
            return [];
        }

        return $this->customerOptionGroup->getOptionAssociations()->map(function (
            CustomerOptionAssociationInterface $association
        ) {
            return $association->getOption();
        })->toArray();
    }
}
