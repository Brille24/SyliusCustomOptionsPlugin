<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Object\PriceImportResult;

interface CustomerOptionPriceImporterInterface
{
    public function import(array $data): PriceImportResult;
}
