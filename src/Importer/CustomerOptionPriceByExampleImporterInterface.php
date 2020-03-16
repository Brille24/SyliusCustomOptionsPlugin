<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;

interface CustomerOptionPriceByExampleImporterInterface
{
    /**
     * @param array $productCodes
     * @param CustomerOptionValuePriceInterface $examplePrice
     *
     * @return array
     */
    public function importForProducts(array $productCodes, CustomerOptionValuePriceInterface $examplePrice): array;
}
