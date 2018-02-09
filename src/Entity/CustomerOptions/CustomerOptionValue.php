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
    use TranslatableTrait;

    /** @var int */
    protected $id;

    /** @var string */
    protected $code;

    /** @var string */
    protected $value;

    /** @var CustomerOptionValuePriceInterface */
    protected $price;

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
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return $this->value;
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
    protected function createTranslation(): TranslationInterface
    {
        return new CustomerOptionValueTranslation();
    }
}