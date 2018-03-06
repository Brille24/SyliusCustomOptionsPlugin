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

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface as COValuePriceInterface;

class CustomerOptionValue implements CustomerOptionValueInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var int|null */
    protected $id;

    /** @var string */
    protected $code;

    /** @var Collection */
    protected $prices;

    /** @var CustomerOptionInterface|null */
    private $customerOption;

    /** @var OrderItemOptionInterface[] */
    private $orders;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->prices = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): string
    {
        return $this->code ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrices(?Collection $prices): void
    {
        $this->prices = $prices;

        foreach ($prices as $price) {
            $price->setCustomerOptionValue($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPrices(): ?Collection
    {
        $prices = new ArrayCollection();

        /** @var CustomerOptionValuePriceInterface $price */
        foreach ($this->prices as $price) {
            if ($price->getProduct() === null) {
                $prices[] = $price;
            }
        }

        return $prices;
    }

    public function getPricesForChannel(ChannelInterface $channel): Collection
    {
        return $this->prices
            ->filter(function (COValuePriceInterface $price) use ($channel) {
                return $price->getChannel()->getId() === $channel->getId();
            });
    }

    /** {@inheritdoc} */
    public function getPriceForChannel(
        ChannelInterface $channel,
        bool $ignoreActive = false
    ): ?CustomerOptionValuePriceInterface {

        $prices = $this->getPricesForChannel($channel);

        if (!$ignoreActive) {
            $prices = $prices->filter(function (COValuePriceInterface $price) { return $price->isActive(); });
        }

        if (count($prices) > 1) {
            // Get the prices with product references (aka. overrides) first
            $prices = $prices->toArray();
            return array_reduce($prices, function ($accumulator, COValuePriceInterface $price): COValuePriceInterface {
                return $price->getProduct() !== null ? $price : $accumulator;
            }, reset($prices));

        } elseif (count($prices) === 1) {
            return $prices->first();
        }

        return null;
    }

    public function addPrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->prices->add($price);

        $price->setCustomerOptionValue($this);
    }

    public function removePrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->prices->removeElement($price);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;
    }

    /**
     * @param string|null $locale
     *
     * @return CustomerOptionValueTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        return $this->doGetTranslation();
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): TranslationInterface
    {
        return new CustomerOptionValueTranslation();
    }

    public function __toString(): string
    {
        return "{$this->getName()}";
    }
}
