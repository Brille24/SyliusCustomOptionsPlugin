<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 13:04
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

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

    /** @var string */
    protected $value;

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
        foreach($this->prices as $price){
            if($price->getProduct() === null){
                $prices[] = $price;
            }
        }

        return $prices;
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
