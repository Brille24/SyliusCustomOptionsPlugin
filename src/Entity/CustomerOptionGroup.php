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

    public function __construct()
    {
        $this->customerOptions = new ArrayCollection();
        $this->initializeTranslationsCollection();
    }

    /**
     * @return CustomerOptionGroupTranslationInterface
     */
    public function createTranslation(): CustomerOptionGroupTranslationInterface
    {
        return new CustomerOptionGroupTranslation();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        /** @var CustomerOptionGroupTranslationInterface $translation */
        $translation = $this->getTranslation();
        return $translation->getName();
    }

    public function setName(?string $name): void
    {
        /** @var CustomerOptionGroupTranslationInterface $translation */
        $translation = $this->getTranslation();
        $translation->setName($name);
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getName();
    }

    /**
     * @return CustomerOption[]
     */
    public function getCustomerOptions(): array
    {
        return $this->customerOptions->toArray();
    }

    /**
     * @param array $customerOptions
     */
    public function setCustomerOptions(array $customerOptions): void
    {
        $customerOptions = array_filter(
            $customerOptions,
            function ($value) { return $value instanceof CustomerOptionInterface; });

        $this->customerOptions = new ArrayCollection($customerOptions);
    }
}