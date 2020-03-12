<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Updater;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;

interface CustomerOptionPriceUpdaterInterface
{
    /**
     * @param string $customerOptionCode
     * @param string $customerOptionValueCode
     * @param string $channelCode
     * @param string $productCode
     * @param string|null $validFrom
     * @param string|null $validTo
     * @param string $type
     * @param int $amount
     * @param float $percent
     *
     * @return CustomerOptionValuePriceInterface
     */
    public function updateForProduct(
        string $customerOptionCode,
        string $customerOptionValueCode,
        string $channelCode,
        string $productCode,
        ?string $validFrom,
        ?string $validTo,
        string $type,
        int $amount,
        float $percent
    ): CustomerOptionValuePriceInterface;
}
