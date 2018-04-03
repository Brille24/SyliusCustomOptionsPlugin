<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;

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
        if (!$this->hasCustomerOptions()) {
            return [];
        }

        return $this->customerOptionGroup->getOptionAssociations()->map(function (
            CustomerOptionAssociationInterface $association
        ) {
            return $association->getOption();
        })->toArray();
    }

    /** {@inheritdoc} */
    public function hasCustomerOptions(): bool
    {
        return $this->customerOptionGroup !== null;
    }
}
