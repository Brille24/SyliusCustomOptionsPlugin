<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Updater;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConstraintViolationException;

interface CustomerOptionPriceUpdaterInterface
{
    /**
     * @param string $customerOptionCode
     * @param string $customerOptionValueCode
     * @param string $channelCode
     * @param ProductInterface $product
     * @param string|null $validFrom
     * @param string|null $validTo
     * @param string $type
     * @param int $amount
     * @param float $percent
     *
     * @return CustomerOptionValuePriceInterface
     *
     * @throws ConstraintViolationException
     */
    public function updateForProduct(
        string $customerOptionCode,
        string $customerOptionValueCode,
        string $channelCode,
        ProductInterface $product,
        ?string $validFrom,
        ?string $validTo,
        string $type,
        int $amount,
        float $percent
    ): CustomerOptionValuePriceInterface;
}
