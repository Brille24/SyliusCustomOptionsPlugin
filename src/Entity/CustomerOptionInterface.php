<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 06.02.18
 * Time: 17:45
 */

namespace Brille24\CustomerOptionsBundle\Entity;


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
     */
    public function setName(?string $name);

    /**
     * @return null|string
     */
    public function getName() : ?string;

    /**
     * @param bool $required
     */
    public function setRequired(bool $required);

    /**
     * @return bool
     */
    public function isRequired() : bool;

    /**
     * @param $values
     */
    public function setValues($values);

    /**
     * @param $value
     */
    public function addValue($value);

    /**
     * @param $value
     */
    public function removeValue($value);

    /**
     * @return Collection
     */
    public function getValues();

    /**
     * @param $groups
     */
    public function setGroups($groups);

    /**
     * @param $group
     */
    public function addGroup($group);

    /**
     * @param $group
     */
    public function removeGroup($group);

    /**
     * @return Collection
     */
    public function getGroups();
}