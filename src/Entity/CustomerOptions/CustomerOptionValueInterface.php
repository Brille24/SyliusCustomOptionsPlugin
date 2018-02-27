<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 10:46
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
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
     * @param Collection|null $prices
     */
    public function setPrices(?Collection $prices);

    /**
     * @return Collection
     */
    public function getPrices(): ?Collection;

    /**
     * @param ChannelInterface $channel
     *
     * @return CustomerOptionValuePriceInterface
     */
    public function getPriceForChannel(ChannelInterface $channel): ?CustomerOptionValuePriceInterface;

    /**
     * @param CustomerOptionValuePriceInterface $price
     */
    public function addPrice(CustomerOptionValuePriceInterface $price): void;

    /**
     * @param CustomerOptionValuePriceInterface $price
     */
    public function removePrice(CustomerOptionValuePriceInterface $price): void;

    /**
     * @param CustomerOptionInterface|null $customerOption
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    /**
     * @return CustomerOptionInterface|null
     */
    public function getCustomerOption(): ?CustomerOptionInterface;
}
