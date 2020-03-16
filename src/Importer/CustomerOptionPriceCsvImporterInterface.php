<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

interface CustomerOptionPriceCsvImporterInterface
{
    /**
     * @param string $source
     *
     * @return array
     */
    public function import(string $source): array;
}
