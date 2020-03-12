<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

interface CustomerOptionPricesImporterInterface
{
    /**
     * @param string $source
     */
    public function importCustomerOptionPrices(string $source): void;
}
