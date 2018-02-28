<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 06.02.18
 * Time: 17:45
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionInterface extends ResourceInterface, TranslatableInterface
{
    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void;

    /**
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void;

    /**
     * @return bool|null
     */
    public function isRequired(): ?bool;

    /**
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void;

    /**
     * @param Collection $values
     */
    public function setValues(array $values): void;

    /**
     * @param CustomerOptionValueInterface $value
     */
    public function addValue(CustomerOptionValueInterface $value): void;

    /**
     * @param CustomerOptionValueInterface $value
     */
    public function removeValue(CustomerOptionValueInterface $value): void;

    /**
     * @return Collection
     */
    public function getValues(): Collection;

    /**
     * @return Collection|Collection[]
     */
    public function getPrices();

    /**
     * @param array $prices
     *
     * @return mixed
     */
    public function setPrices(array $prices);

    /**
     * @param CustomerOptionAssociationInterface $assoc
     */
    public function setGroupAssociations(CustomerOptionAssociationInterface $assoc): void;

    /**
     * @return CustomerOptionAssociationInterface[]
     */
    public function getGroupAssociations(): ArrayCollection;

    public function addGroupAssociation(CustomerOptionAssociationInterface $assoc): void;

    public function removeGroupAssociation(CustomerOptionAssociationInterface $assoc): void;
}
