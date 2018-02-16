<?php
/**
 * Created by PhpStorm.
 * User: jtolkemit
 * Date: 07.02.18
 * Time: 13:04
 */

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class CustomerOptionValue implements CustomerOptionValueInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var int */
    protected $id;

    /** @var string */
    protected $code;

    /** @var string */
    protected $value;

    /** @var CustomerOptionValuePriceInterface */
    protected $price;

    /** @var CustomerOptionInterface|null */
    private $customerOption;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->price = new CustomerOptionValuePrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(string $code)
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
    public function setPrice(CustomerOptionValuePriceInterface $price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(): CustomerOptionValuePriceInterface
    {
        return $this->price;
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
     * @param null|string $locale
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
        return "{$this->getName()} ({$this->price})";
    }
}