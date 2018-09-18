<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ProductCustomerOptionCapableTrait
{
    /** @var CustomerOptionGroupInterface|null */
    protected $customerOptionGroup;

    /** @var Collection|CustomerOptionValuePriceInterface[] */
    protected $customerOptionValuePrices;

    public function __construct()
    {
        $this->customerOptionValuePrices = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface
    {
        return $this->customerOptionGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void
    {
        $this->customerOptionGroup = $customerOptionGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomerOptions(): bool
    {
        return $this->customerOptionGroup !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionValuePrices(): Collection
    {
        return $this->customerOptionValuePrices;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOptionValuePrices(?Collection $prices): void
    {
        if ($prices === null) {
            $this->customerOptionValuePrices->clear();

            return;
        }

        $this->customerOptionValuePrices = $prices;

        foreach ($prices as $price) {
            $price->setProduct($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->customerOptionValuePrices->add($price);
        $price->setProduct($this);
    }

    /**
     * {@inheritdoc}
     */
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
        $customerOptionGroup = $this->customerOptionGroup;
        if ($customerOptionGroup === null) {
            return [];
        }

        return $customerOptionGroup->getOptionAssociations()->map(function (
            CustomerOptionAssociationInterface $association
        ) {
            return $association->getOption();
        })->toArray();
    }
}
