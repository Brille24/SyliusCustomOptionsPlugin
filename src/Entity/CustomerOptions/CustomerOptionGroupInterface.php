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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionGroupInterface extends CodeAwareInterface, ResourceInterface, TranslatableInterface
{
    /**
     * @return string|null
     */
    public function getName(): ? string;

    /**
     * @param string|null $name
     */
    public function setName(? string $name): void;

    /**
     * @return Collection|CustomerOptionAssociationInterface[]
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

    /**
     * @* @param ProductInterface[] $product
     * @param array $products
     */
    public function setProducts(array $products): void;

    /**
     * @param ProductInterface $product
     */
    public function addProduct(ProductInterface $product): void;

    /**
     * @return ValidatorInterface[]|Collection
     */
    public function getValidators(): Collection;

    /**
     * @param ValidatorInterface $validator
     */
    public function addValidator(ValidatorInterface $validator): void;

    /**
     * @param ValidatorInterface $validator
     */
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
