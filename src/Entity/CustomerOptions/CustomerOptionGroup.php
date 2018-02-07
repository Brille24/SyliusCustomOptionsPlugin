<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\TranslatableTrait;

class CustomerOptionGroup implements CustomerOptionGroupInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
    }

    /** @var int */
    private $id;

    /** @var string */
    private $code;

    /** @var ArrayCollection */
    private $customerOptions;

    /** @var ArrayCollection */
    private $products;

    public function __construct()
    {
        $this->customerOptions = new ArrayCollection();
        $this->products        = new ArrayCollection();
        $this->initializeTranslationsCollection();
    }

    /**
     * @return CustomerOptionGroupTranslationInterface
     */
    public function createTranslation(): CustomerOptionGroupTranslationInterface
    {
        return new CustomerOptionGroupTranslation();
    }

    /** {@inheritdoc} */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function getName(): ?string
    {
        /** @var CustomerOptionGroupTranslationInterface $translation */
        $translation = $this->getTranslation();
        return $translation->getName();
    }

    /** {@inheritdoc} */
    public function setName(?string $name): void
    {
        /** @var CustomerOptionGroupTranslationInterface $translation */
        $translation = $this->getTranslation();
        $translation->setName($name);
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
    public function getCustomerOptions(): array
    {
        return $this->customerOptions->toArray();
    }

    /** {@inheritdoc} */
    public function setCustomerOptions(array $customerOptions): void
    {
        $customerOptions = array_filter(
            $customerOptions,
            function ($value) { return $value instanceof CustomerOptionInterface; });

        $this->customerOptions = new ArrayCollection($customerOptions);
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

    public function __toString(): string
    {
        return (string)$this->getName();
    }
}