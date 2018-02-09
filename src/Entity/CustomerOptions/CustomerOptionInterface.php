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
     * @param bool $required
     */
    public function setRequired(bool $required);

    /**
     * @return bool
     */
    public function isRequired() : bool;

    /**
     * @param Collection $values
     */
    public function setValues($values);

    /**
     * @param CustomerOptionValueInterface $value
     */
    public function addValue($value);

    /**
     * @param CustomerOptionValueInterface $value
     */
    public function removeValue($value);

    /**
     * @return Collection
     */
    public function getValues();

    /**
     * @param CustomerOptionAssociationInterface $assoc
     */
    public function setGroupAssociation($assoc);

    /**
     * @return CustomerOptionAssociationInterface
     */
    public function getGroupAssociation();
}