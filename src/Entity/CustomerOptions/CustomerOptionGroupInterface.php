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

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionGroupInterface extends CodeAwareInterface, ResourceInterface, TranslatableInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return CustomerOptionAssociationInterface[]
     */
    public function getOptionAssociations(): Collection;

    /**
     * @param CustomerOptionAssociationInterface $association
     */
    public function addOptionAssociation(CustomerOptionAssociationInterface $association): void;

    /**
     * @param CustomerOptionAssociationInterface $association
     */
    public function removeOptionAssociation(CustomerOptionAssociationInterface $association): void;

    /**
     * @return bool
     */
    public function hasOptionAssociations(): bool;

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array;

    public function setProducts(array $products): void;

    public function addProduct(ProductInterface $product): void;

    public function getValidators(): array;

    public function setValidators(array $validators): void;

    public function addValidator(ValidatorInterface $validator): void;

    public function removeValidator(ValidatorInterface $validator): void;

    /**
     * @return CustomerOptionInterface[]
     */
    public function getOptions(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}
