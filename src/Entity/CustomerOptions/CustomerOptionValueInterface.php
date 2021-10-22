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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface CustomerOptionValueInterface extends ResourceInterface, TranslatableInterface
{
    public function setCode(string $code): void;

    /**
     * @return string
     */
    public function getCode(): ?string;

    public function setName(string $name): void;

    /**
     * @return string
     */
    public function getName(): ?string;

    public function setPrices(?Collection $prices): void;

    public function getPrices(): Collection;

    public function getPricesForChannel(ChannelInterface $channel): Collection;

    /**
     * @deprecated Please use {@see CustomerOptionValuePriceRepository::getPriceForChannel()} instead.
     *
     * @return CustomerOptionValuePriceInterface
     */
    public function getPriceForChannel(
        ChannelInterface $channel,
        ProductInterface $product,
        bool $ignoreActive = false
    ): ?CustomerOptionValuePriceInterface;

    public function addPrice(CustomerOptionValuePriceInterface $price): void;

    public function removePrice(CustomerOptionValuePriceInterface $price): void;

    public function setCustomerOption(?CustomerOptionInterface $customerOption): void;

    public function getCustomerOption(): ?CustomerOptionInterface;
}
