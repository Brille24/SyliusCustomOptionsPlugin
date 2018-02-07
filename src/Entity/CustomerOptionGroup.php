<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Entity;

use Sylius\Component\Resource\Model\TranslatableTrait;

class CustomerOptionGroup implements CustomerOptionGroupInterface
{
    use TranslatableTrait;

    /** @var int */
    private $id;

    /** @var string */
    private $code;

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
}