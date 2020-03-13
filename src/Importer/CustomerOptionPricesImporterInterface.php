<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

interface CustomerOptionPricesImporterInterface
{
    /**
     * @param string $source
     *
     * @return array
     */
    public function importCustomerOptionPrices(string $source): array;
}
