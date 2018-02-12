<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
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

    /** @var ArrayCollection */
    private $optionAssociations;

    /** @var ArrayCollection */
    private $products;

    public function __construct()
    {
        $this->optionAssociations = new ArrayCollection();
        $this->products           = new ArrayCollection();
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
    public function getOptionAssociations(): array
    {
        return $this->optionAssociations->toArray();
    }

    /** {@inheritdoc} */
    public function setOptionAssociations(array $associations): void
    {
        $associations = array_filter(
            $associations,
            function ($value) { return $value instanceof CustomerOptionAssociationInterface; });

        $this->optionAssociations = new ArrayCollection($associations);
    }

    /** {@inheritdoc} */
    public function getProducts(): array
    {
        return $this->products->toArray();
    }

    /**
     * @param array $customerOptions
     */
    public function setProducts(array $customerOptions): void
    {
        $customerOptions = array_filter(
            $customerOptions,
            function ($value) { return $value instanceof ProductInterface; });

        $this->products = new ArrayCollection($customerOptions);
    }

    //<editor-fold "Translations">

    /**
     * @param null|string $locale
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
        return (string)$this->getName();
    }
}