<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Reader;

interface CsvReaderInterface
{
    /**
     * @param string $path
     *
     * @return array
     */
    public function readCsv(string $path): array;

    /**
     * @param array $row
     * @param array $requiredFields
     *
     * @return bool
     */
    public function isRowValid(array $row, array $requiredFields): bool;
}
