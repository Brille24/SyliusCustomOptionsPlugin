<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Reader;

use Webmozart\Assert\Assert;

class CsvReader implements CsvReaderInterface
{
    /** {@inheritdoc} */
    public function readCsv(string $path): array
    {
        $file = fopen($path, 'rb');

        Assert::resource($file);

        $header            = null;
        $csv               = [];
        $currentLineNumber = 0;
        while ($row = fgetcsv($file)) {
            ++$currentLineNumber;

            // Use the first row as array keys
            if (null === $header) {
                $header = $row;

                continue;
            }

            // Replace empty strings with null
            $row = array_map(static fn ($value) => '' !== $value ? $value : null, $row);

            $csv[$currentLineNumber] = array_combine($header, $row);
        }

        return $csv;
    }
}
