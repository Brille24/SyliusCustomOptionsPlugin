<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Sylius\Component\Order\Model\OrderItemInterface as SyliusOrderItemInterface;

trait OrderItemCustomerOptionCapableTrait
{
    /** @var Collection|OrderItemOptionInterface[] */
    protected $configuration;

    public function __construct()
    {
        $this->configuration = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerOptionConfiguration(array $configuration): void
    {
        $this->configuration = new ArrayCollection($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionConfiguration(bool $assoc=false): array
    {
        /** @var OrderItemInterface[] $orderItemOptionList */
        $orderItemOptionList = $this->configuration->toArray();
        if ($assoc) {
            $orderItemOptionList = array_combine(
                array_map(function (OrderItemOptionInterface $config) {
                    return $config->getCustomerOptionCode();
                }, $orderItemOptionList),
                $orderItemOptionList
            );
            if ($orderItemOptionList === false) {
                throw new Exception('Could not build associative array');
            }
        }

        return $orderItemOptionList;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerOptionConfigurationAsSimpleArray(): array
    {
        $result = [];
        /** @var OrderItemOptionInterface $config */
        foreach ($this->configuration as $config) {
            $result[$config->getCustomerOptionCode()] = $config->getScalarValue();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $customerOptionAwareUnitPrice = $this->applyConfigurationPrices($unitPrice);
        $this->unitPrice              = $customerOptionAwareUnitPrice;
        $this->recalculateUnitsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function equals(SyliusOrderItemInterface $item): bool
    {
        $parentEquals = parent::equals($item);

        if (!$parentEquals && !$item instanceof OrderItemInterface) {
            return $parentEquals;
        }

        if ($item instanceof self) {
            $product = $item->getProduct();
        } else {
            $product = null;
        }

        return ($product instanceof ProductInterface) ? !$product->hasCustomerOptions() : true;
    }

    /**
     * Applies the configuration pricing and returns the new price.
     *
     * @param int $basePrice
     * @param int $quantity
     *
     * @return int
     */
    protected function applyConfigurationPrices(int $basePrice, int $quantity = 1): int
    {
        $result = $basePrice;

        /** @var OrderItemOptionInterface $value */
        foreach ($this->configuration as $value) {
            if ($value->getCustomerOptionValue() === null) {
                continue; // Skip all values where the value is not an object (value objects can be priced)
            }

            if ($value->getPricingType() === CustomerOptionValuePrice::TYPE_PERCENT) {
                $result += $basePrice * $value->getPercent();
            } else {
                $result += $value->getFixedPrice() * $quantity;
            }
        }

        return (int) round($result, 0);
    }
}
