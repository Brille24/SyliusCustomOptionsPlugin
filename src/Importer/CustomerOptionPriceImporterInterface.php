<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Object\PriceImportResult;

interface CustomerOptionPriceImporterInterface
{
    /**
     * @param array $data
     *
     * @return PriceImportResult
     */
    public function import(array $data): PriceImportResult;
}
