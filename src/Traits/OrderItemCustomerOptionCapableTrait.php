<?php
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;


use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

trait OrderItemCustomerOptionCapableTrait
{
    /** @var Collection */
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
    public function getCustomerOptionConfiguration(): array
    {
        return $this->configuration->toArray();
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
    public function getSubtotal(): int
    {
        $basePrice = parent::getSubtotal();

        return $this->applyConfigurationPrices($basePrice, $this->getQuantity());
    }

    /**
     * {@inheritdoc}
     */
    public function equals(OrderItemInterface $item): bool
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
     * {@inheritdoc}
     */
    public function recalculateUnitsTotal(): void
    {
        $this->unitsTotal = 0;

        /** @var OrderItemUnitInterface $unit */
        foreach ($this->units as $unit) {
            $this->unitsTotal += $this->applyConfigurationPrices($unit->getTotal());
        }

        $this->recalculateTotal();
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