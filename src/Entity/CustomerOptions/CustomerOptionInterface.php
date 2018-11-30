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
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $code
     */
    public function setCode(string $code): void;

    /**
     * @return string
     */
    public function getCode(): string;

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
     * @param CustomerOptionValueInterface[] $values
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
     * @return array
     */
    public function getPrices(): array;

    /**
     * @param array $prices
     *
     * @return mixed
     */
    public function setPrices(array $prices);

    /**
     * @param ArrayCollection $assoc
     */
    public function setGroupAssociations(ArrayCollection $assoc): void;

    /**
     * @return ArrayCollection
     */
    public function getGroupAssociations(): ArrayCollection;

    /**
     * @param CustomerOptionAssociationInterface $assoc
     */
    public function addGroupAssociation(CustomerOptionAssociationInterface $assoc): void;

    /**
     * @param CustomerOptionAssociationInterface $assoc
     */
    public function removeGroupAssociation(CustomerOptionAssociationInterface $assoc): void;
}
