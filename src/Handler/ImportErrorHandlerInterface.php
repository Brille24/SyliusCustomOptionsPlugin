<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

interface ImportErrorHandlerInterface
{
    public function handleErrors(array $errors, array $extraData): void;
}
