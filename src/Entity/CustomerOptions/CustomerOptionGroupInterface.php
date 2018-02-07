<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Entity\CustomerOptions;

use Brille24\CustomerOptionsBundle\Entity\ProductInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionGroupInterface extends CodeAwareInterface, ResourceInterface
{
    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return CustomerOptionInterface[]
     */
    public function getCustomerOptions(): array;

    /**
     * @param array $customerOptions
     */
    public function setCustomerOptions(array $customerOptions): void;

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}