<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 06.02.18
 * Time: 17:45
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionInterface extends ResourceInterface, TranslatableInterface
{
    /**
     * @param null|string $type
     */
    public function setType(?string $type);

    /**
     * @return null|string
     */
    public function getType() : ?string;

    /**
     * @param null|string $code
     */
    public function setCode(?string $code);

    /**
     * @return null|string
     */
    public function getCode() : ?string;

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
    public function setRequired(bool $required);

    /**
     * @return null|bool
     */
    public function isRequired() : ?bool;

    /**
     * @param Collection $values
     */
    public function setValues(array $values);

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
    public function getValues();

    /**
     * @return Collection|CustomerOptionValuePriceInterface[]
     */
    public function getPrices();

    /**
     * @param array $prices
     * @return mixed
     */
    public function setPrices(array $prices);

    /**
     * @param CustomerOptionAssociationInterface $assoc
     */
    public function setGroupAssociations(CustomerOptionAssociationInterface $assoc);

    /**
     * @return CustomerOptionAssociationInterface
     */
    public function getGroupAssociations();
}