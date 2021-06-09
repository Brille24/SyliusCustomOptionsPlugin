<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Reader;

interface CsvReaderInterface
{
    /**
     * @param string $path
     *
     * @return array<array<string, string>>
     */
    public function readCsv(string $path): array;
}
