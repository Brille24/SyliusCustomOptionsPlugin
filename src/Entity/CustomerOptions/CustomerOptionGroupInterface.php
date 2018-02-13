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

    public function addOptionAssociation(CustomerOptionAssociationInterface $association);

    public function removeOptionAssociation(CustomerOptionAssociationInterface $association);

    public function hasOptionAssociations();

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}