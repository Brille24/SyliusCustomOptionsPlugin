<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

interface CustomerOptionPriceImporterInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function import(array $data): array;
}
