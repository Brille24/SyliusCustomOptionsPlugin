<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Reader;

interface CsvReaderInterface
{
    public function readCsv(string $path): array;
}
