<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionGroupTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Product as BaseProduct;

class Product extends BaseProduct implements ProductInterface
{
    use CustomerOptionGroupTrait {
        __construct as protected initializeCustomerOptionGroup;
    }

    public function __construct()
    {
        parent::__construct();
        $this->initializeCustomerOptionGroup();
    }

    /** @var Collection|CustomerOptionValuePriceInterface[] */
    protected $customerOptionPrices;

    public function getCustomerOptionPrices(): Collection
    {
        return $this->customerOptionPrices;
    }

    public function setCustomerOptionPrices(Collection $prices)
    {
        $this->customerOptionPrices = $prices;
    }

    public function getCustomerOptions(): Collection
    {
        $options = new ArrayCollection();

        if($this->customerOptionGroup !== null) {
            foreach ($this->customerOptionGroup->getOptionAssociations() as $assoc) {
                $options[] = $assoc->getOption();
            }
        }

        return $options;
    }
}