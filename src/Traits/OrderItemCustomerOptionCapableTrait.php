<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Traits;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Order\Model\OrderItemInterface as SyliusOrderItemInterface;

trait OrderItemCustomerOptionCapableTrait
{
    /**
     * @var Collection|OrderItemOptionInterface[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface",
     *     mappedBy="orderItem",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $configuration;

    public function __construct()
    {
        $this->configuration = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function setCustomerOptionConfiguration(array $configuration): void
    {
        $this->configuration = new ArrayCollection($configuration);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerOptionConfiguration(bool $assoc = false): array
    {
        /** @var OrderItemOptionInterface[] $orderItemOptionList */
        $orderItemOptionList = $this->configuration->toArray();
        if ($assoc) {
            /** @var array<int, mixed> $assocArray */
            $assocArray = [];

            foreach ($orderItemOptionList as $orderItemOption) {
                // Multiselect needs to be an array
                /** @var CustomerOptionInterface $customerOption */
                $customerOption = $orderItemOption->getCustomerOption();
                if ($customerOption->getType() === CustomerOptionTypeEnum::MULTI_SELECT) {
                    $assocArray[$orderItemOption->getCustomerOptionCode()][] = $orderItemOption;
                } else {
                    $assocArray[$orderItemOption->getCustomerOptionCode()] = $orderItemOption;
                }
            }

            $orderItemOptionList = $assocArray;
        }

        return $orderItemOptionList;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function equals(SyliusOrderItemInterface $item): bool
    {
        // If the product doesn't match for the Sylius implementation then it's not the same.
        if (!parent::equals($item)) {
            return false;
        }

        if (!$item instanceof self) {
            return false;
        }

        /** @var OrderItemOptionInterface[] $itemCustomerConfiguration */
        $itemCustomerConfiguration    = $item->getCustomerOptionConfiguration(true);
        $curItemCustomerConfiguration = $this->getCustomerOptionConfiguration(true);

        // configuration array size needs to be identical
        if (count($itemCustomerConfiguration) !== count($curItemCustomerConfiguration)) {
            return false;
        }

        // configuration array keys need to match
        $diff = array_diff_key($itemCustomerConfiguration, $curItemCustomerConfiguration);
        if (count($diff) !== 0) {
            return false;
        }

        // iterate over all options and compare their values
        foreach ($itemCustomerConfiguration as $code => $value) {
            $curItemCustomerConfigurationOptionValue = (is_array($curItemCustomerConfiguration[$code]) ? current($curItemCustomerConfiguration[$code]) : $curItemCustomerConfiguration[$code])->getOptionValue();
            $valueOptionValue                        = (is_array($value) ? current($value) : $value)->getOptionValue();

            if ($curItemCustomerConfigurationOptionValue !== $valueOptionValue) {
                return false;
            }
        }

        return true;
    }
}
