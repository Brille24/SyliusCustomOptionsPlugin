<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Object;

class PriceImportResult
{
    public function __construct(private int $imported, private int $failed, private array $errors)
    {
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getFailed(): int
    {
        return $this->failed;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
