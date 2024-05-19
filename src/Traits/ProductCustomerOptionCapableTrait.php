<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait ProductCustomerOptionCapableTrait
{
    #[ORM\ManyToOne(targetEntity: CustomerOptionGroupInterface::class, inversedBy: "products")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    protected ?CustomerOptionGroupInterface $customerOptionGroup = null;

    /**
     * @var Collection<CustomerOptionValuePriceInterface>
     */
    #[ORM\OneToMany(
        targetEntity: CustomerOptionValuePriceInterface::class,
        mappedBy: "product",
        orphanRemoval: true,
        cascade: ["persist", "remove"]
    )]
    protected Collection $customerOptionValuePrices;

    public function __construct()
    {
        $this->customerOptionValuePrices = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface
    {
        return $this->customerOptionGroup;
    }

    /**
     * @inheritdoc
     */
    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void
    {
        $this->customerOptionGroup = $customerOptionGroup;
    }

    /**
     * @inheritdoc
     */
    public function hasCustomerOptions(): bool
    {
        return $this->customerOptionGroup !== null;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerOptionValuePrices(): Collection
    {
        // We can not rely on the prices being set because Doctrine doesn't call the constructor for loaded entited
        return $this->customerOptionValuePrices ?? new ArrayCollection();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function addCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->customerOptionValuePrices->add($price);
        $price->setProduct($this);
    }

    /**
     * @inheritdoc
     */
    public function removeCustomerOptionValuePrice(CustomerOptionValuePriceInterface $price): void
    {
        $this->customerOptionValuePrices->removeElement($price);
        $price->setProduct(null);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerOptions(): array
    {
        $customerOptionGroup = $this->customerOptionGroup;
        if ($customerOptionGroup === null) {
            return [];
        }

        return $customerOptionGroup->getOptionAssociations()->map(static fn (CustomerOptionAssociationInterface $association): ?CustomerOptionInterface => $association->getOption())->toArray();
    }
}
