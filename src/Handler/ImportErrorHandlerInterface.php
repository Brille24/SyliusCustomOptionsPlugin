<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

interface ImportErrorHandlerInterface
{
    /**
     * @param array $errors
     */
    public function handleErrors(array $errors): void;
}
