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
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    public function setType(?string $type): void;

    public function getType(): string;

    public function getCode(): string;

    public function setName(?string $name): void;

    public function getName(): ?string;

    public function setRequired(bool $required): void;

    public function isRequired(): bool;

    public function getConfiguration(): array;

    public function setConfiguration(array $configuration): void;

    /**
     * @param CustomerOptionValueInterface[] $values
     */
    public function setValues(array $values): void;

    public function addValue(CustomerOptionValueInterface $value): void;

    public function removeValue(CustomerOptionValueInterface $value): void;

    public function getValues(): Collection;

    public function getPrices(): array;

    /**
     * @return mixed
     */
    public function setPrices(array $prices);

    public function setGroupAssociations(ArrayCollection $assoc): void;

    public function getGroupAssociations(): ArrayCollection;

    public function addGroupAssociation(CustomerOptionAssociationInterface $assoc): void;

    public function removeGroupAssociation(CustomerOptionAssociationInterface $assoc): void;
}
