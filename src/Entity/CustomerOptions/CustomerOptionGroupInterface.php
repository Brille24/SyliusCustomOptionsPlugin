<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionGroupInterface extends CodeAwareInterface, ResourceInterface, TranslatableInterface
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
     * @return CustomerOptionAssociationInterface[]
     */
    public function getOptionAssociations(): Collection;

    /**
     * @param CustomerOptionAssociationInterface $association
     *
     * @return void
     */
    public function addOptionAssociation(CustomerOptionAssociationInterface $association): void;

    /**
     * @param CustomerOptionAssociationInterface $association
     *
     * @return void
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
     * @return string
     */
    public function __toString(): string;
}