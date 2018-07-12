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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionValueInterface extends ResourceInterface, TranslatableInterface
{
    /**
     * @param string $code
     */
    public function setCode(string $code): void;

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
    public function getName(): ?string;

    /**
     * @param Collection|null $prices
     */
    public function setPrices(?Collection $prices): void;

    /**
     * @return Collection
     */
    public function getPrices(): Collection;

    /**
     * @param ChannelInterface $channel
     * @param bool             $ignoreActive
     *
     * @return CustomerOptionValuePriceInterface
     */
    public function getPriceForChannel(
        ChannelInterface $channel,
        bool $ignoreActive = false
    ): ?CustomerOptionValuePriceInterface;

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
