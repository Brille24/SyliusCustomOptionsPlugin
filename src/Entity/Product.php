<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ProductInterface
{
    use CustomerOptionableTrait {
        __construct as protected initializeCustomerOptionGroup;
    }

    /** @var Collection|CustomerOptionValuePriceInterface[] */
    protected $customerOptionValuePrices;

    public function __construct()
    {
        parent::__construct();
        $this->initializeCustomerOptionGroup();
        $this->customerOptionValuePrices = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionValuePrices(): ?Collection
    {
        return $this->customerOptionValuePrices;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOptionValuePrices(?Collection $prices)
    {
        $this->customerOptionValuePrices = $prices;

        foreach ($prices as $price) {
            $price->setProduct($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptions(): array
    {
        $options = [];

        if ($this->customerOptionGroup !== null) {
            foreach ($this->customerOptionGroup->getOptionAssociations() as $assoc) {
                $options[] = $assoc->getOption();
            }
        }

        return $options;
    }
}
