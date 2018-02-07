<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 12:23
 */

namespace Brille24\CustomerOptionsBundle\Entity;


use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionCustomerOptionGroupInterface extends ResourceInterface
{
    /**
     * @return CustomerOptionInterface|null
     */
    public function getCustomerOption() : ?CustomerOptionInterface;

    /**
     * @param CustomerOptionInterface $option
     */
    public function setCustomerOption(CustomerOptionInterface $option) : void;

    /**
     * @return CustomerOptionGroupInterface|null
     */
    public function getCustomerOptionGroup() : ?CustomerOptionGroupInterface;

    /**
     * @param CustomerOptionGroupInterface $group
     */
    public function setCustomerOptionGroup(CustomerOptionGroupInterface $group) : void;

    /**
     * @return int|null
     */
    public function getPosition() : ?int;

    /**
     * @param int $position
     */
    public function setPosition(int $position) : void;
}