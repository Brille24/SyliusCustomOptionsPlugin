<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:46
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionValueInterface extends ResourceInterface, TranslatableInterface
{
    /**
     * @param string $code
     */
    public function setCode(string $code);

    /**
     * @return string
     */
    public function getCode(): ?string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $value
     */
    public function setValue(string $value);

    /**
     * @return string
     */
    public function getValue(): ?string;

    /**
     * @param Collection $prices
     */
    public function setPrices(?Collection $prices);

    /**
     * @return Collection
     */
    public function getPrices(): ?Collection;

    /**
     * @param CustomerOptionInterface|null $customerOption
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption):void;

    /**
     * @return CustomerOptionInterface|null
     */
    public function getCustomerOption(): ?CustomerOptionInterface;
}