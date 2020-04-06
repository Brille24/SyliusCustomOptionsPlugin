<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

interface ImportErrorHandlerInterface
{
    /**
     * @param string $type
     * @param array $errors
     * @param array $extraData
     */
    public function handleErrors(string $type, array $errors, array $extraData): void;
}
