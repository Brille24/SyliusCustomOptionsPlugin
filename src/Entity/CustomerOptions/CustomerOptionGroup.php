<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class CustomerOptionGroup implements CustomerOptionGroupInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var int */
    private $id;

    /** @var string */
    private $code;

    /** @var PersistentCollection */
    private $optionAssociations;

    /** @var ArrayCollection */
    private $products;

    public function __construct()
    {
        $this->optionAssociations = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->initializeTranslationsCollection();
    }

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /** {@inheritdoc} */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /** {@inheritdoc} */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /** {@inheritdoc} */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /** {@inheritdoc} */
    public function getOptionAssociations(): Collection
    {
        return $this->optionAssociations;
    }

    public function addOptionAssociation(CustomerOptionAssociationInterface $association): void
    {
        $this->optionAssociations->add($association);

        $association->setGroup($this);
    }

    public function removeOptionAssociation(CustomerOptionAssociationInterface $association): void
    {
        $this->optionAssociations->removeElement($association);
    }

    public function hasOptionAssociations(): bool
    {
        return !$this->optionAssociations->isEmpty();
    }

    /** {@inheritdoc} */
    public function getProducts(): array
    {
        return $this->products->getValues();
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products): void
    {
        $products = array_filter(
            $products,
            function ($value) { return $value instanceof ProductInterface; });

        $this->products = new ArrayCollection($products);

        /** @var ProductInterface $product */
        foreach ($products as $product){
            $product->setCustomerOptionGroup($this);
        }
    }

    /**
     * Returns the first $count options of the group
     *
     * @param int $count (default: 5)
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->optionAssociations
            ->map(function (CustomerOptionAssociationInterface $association) {
                return $association->getOption();
            })->toArray();
    }

    //<editor-fold "Translations">

    /**
     * @param string|null $locale
     *
     * @return CustomerOptionTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    /**
     * @return CustomerOptionGroupTranslationInterface
     */
    public function createTranslation(): CustomerOptionGroupTranslationInterface
    {
        return new CustomerOptionGroupTranslation();
    }

    //</editor-fold>
    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
