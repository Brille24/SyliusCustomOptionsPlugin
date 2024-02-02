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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface as COValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

#[ORM\Entity(repositoryClass: CustomerOptionValueRepository::class)]
#[ORM\Table(name: 'brille24_customer_option_value')]
#[ORM\UniqueConstraint(name: 'unique_customer_option_code', columns: ['customerOption_id', 'code'])]
class CustomerOptionValue implements CustomerOptionValueInterface, \Stringable
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    protected string $code;

    #[ORM\OneToMany(mappedBy: 'customerOptionValue', targetEntity: COValuePriceInterface::class, cascade: ['persist', 'remove'])]
    protected Collection $prices;

    #[ORM\ManyToOne(targetEntity: CustomerOptionInterface::class, inversedBy: 'values')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected ?CustomerOptionInterface $customerOption = null;

    /** @var OrderItemOptionInterface[] */
    protected $orders;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->prices = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return $this->code ?? '';
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        /** @var CustomerOptionValueTranslationInterface $translation */
        $translation = $this->getTranslation();

        return $translation->getName();
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        /** @var CustomerOptionValueTranslationInterface $translation */
        $translation = $this->getTranslation();
        $translation->setName($name);
    }

    /**
     * @inheritdoc
     */
    public function setPrices(?Collection $prices): void
    {
        if ($prices === null) {
            $this->prices->clear();

            return;
        }

        $this->prices = $prices;

        foreach ($prices as $price) {
            $price->setCustomerOptionValue($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPrices(): Collection
    {
        return $this->prices->filter(static fn (CustomerOptionValuePriceInterface $price): bool => $price->getProduct() === null);
    }

    public function getPricesForChannel(ChannelInterface $channel): Collection
    {
        $priceIsInChannel = static function (COValuePriceInterface $price) use ($channel): bool {
            $channelOfPrice = $price->getChannel();
            if ($channelOfPrice === null) {
                return false;
            }

            return $channelOfPrice->getId() === $channel->getId();
        };

        return $this->prices->filter($priceIsInChannel);
    }

    /** @inheritdoc */
    public function getPriceForChannel(
        ChannelInterface $channel,
        ProductInterface $product,
        bool $ignoreActive = false,
    ): ?CustomerOptionValuePriceInterface {
        $prices = $this->getPricesForChannel($channel);

        if (!$ignoreActive) {
            $prices = $prices->filter(static fn (COValuePriceInterface $price): bool => $price->isActive());
        }

        if (count($prices) === 1) {
            return $prices->first();
        }

        if (count($prices) > 1) {
            // Get the prices with product references (aka. overrides) first
            $prices = $prices->toArray();

            return array_reduce(
                $prices,
                static function ($accumulator, COValuePriceInterface $price) use ($product): COValuePriceInterface {
                    $customerOptionProduct = $price->getProduct();
                    if ($customerOptionProduct !== null && $customerOptionProduct->getCode() === $product->getCode()) {
                        return $price;
                    }

                    return $accumulator;
                },
                reset($prices),
            );
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
     * @inheritdoc
     */
    public function getCustomerOption(): ?CustomerOptionInterface
    {
        return $this->customerOption;
    }

    /**
     * @inheritdoc
     */
    public function setCustomerOption(?CustomerOptionInterface $customerOption): void
    {
        $this->customerOption = $customerOption;
    }

    public function getTranslation(?string $locale = null): TranslationInterface
    {
        return $this->doGetTranslation();
    }

    /**
     * @return OrderItemOptionInterface[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @inheritdoc
     */
    protected function createTranslation(): TranslationInterface
    {
        return new CustomerOptionValueTranslation();
    }

    public function __toString(): string
    {
        return (string) "{$this->getName()}";
    }
}
