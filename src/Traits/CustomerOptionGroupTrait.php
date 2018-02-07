<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Traits;

use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionGroup;


/**
 * Trait CustomerOptionTrait
 *
 * This is a trait to attach customer options to an object
 *
 * @package Brille24\CustomerOptionsBundle\Traits
 * @see     CustomerOptionGroupInterface
 */
trait CustomerOptionGroupTrait
{
    /** @var CustomerOptionGroup|null */
    private $customerOptionGroup;

    public function __construct() { }

    /** {@inheritdoc} */
    public function getCustomerOptionGroup(): ?CustomerOptionGroup
    {
        return $this->customerOptionGroup;
    }

    /** {@inheritdoc} */
    public function setCustomerOptionGroup(?CustomerOptionGroup $customerOptionGroup): void
    {
        $this->customerOptionGroup = $customerOptionGroup;
    }
}