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

namespace Brille24\SyliusCustomerOptionsPlugin\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Traits\CustomerOptionableTrait;
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

    public function addCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->customerOptionValuePrices->add($price);
        $price->setProduct($this);
    }

    public function removeCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->customerOptionValuePrices->removeElement($price);
        $price->setProduct(null);
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
