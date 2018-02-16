<?php
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
     * @param null|string $type
     */
    public function setType(?string $type): void;

    /**
     * @return null|string
     */
    public function getType(): ?string;

    /**
     * @param null|string $code
     */
    public function setCode(?string $code): void;

    /**
     * @return null|string
     */
    public function getCode(): ?string;

    /**
     * @param null|string $name
     *
     * @return void
     */
    public function setName(?string $name): void;

    /**
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void;

    /**
     * @return null|bool
     */
    public function isRequired(): ?bool;

    /**
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * @param array $configuration
     *
     * @return void
     */
    public function setConfiguration(array $configuration): void;

    /**
     * @param Collection $values
     */
    public function setValues(array $values): void;

    /**
     * @param CustomerOptionValueInterface $value
     */
    public function addValue(CustomerOptionValueInterface $value);

    /**
     * @param CustomerOptionValueInterface $value
     */
    public function removeValue(CustomerOptionValueInterface $value);

    /**
     * @return Collection
     */
    public function getValues(): Collection;

    /**
     * @param CustomerOptionAssociationInterface $assoc
     */
    public function setGroupAssociations(CustomerOptionAssociationInterface $assoc): void;

    /**
     * @return CustomerOptionAssociationInterface[]
     */
    public function getGroupAssociations(): ArrayCollection;
}