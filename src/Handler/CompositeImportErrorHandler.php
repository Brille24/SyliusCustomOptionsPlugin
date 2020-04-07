<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

use Webmozart\Assert\Assert;

class CompositeImportErrorHandler implements ImportErrorHandlerInterface
{
    /** @var ImportErrorHandlerInterface[] */
    protected $errorHandlers = [];

    public function addErrorHandler(ImportErrorHandlerInterface $errorHandler, string $type): void
    {
        $this->errorHandlers[$type] = $errorHandler;
    }

    /** {@inheritdoc} */
    public function handleErrors(string $type, array $errors, array $extraData): void
    {
        Assert::keyExists($this->errorHandlers, $type, sprintf('No registered ImportErrorHandler for type: %s', $type));

        $this->errorHandlers[$type]->handleErrors($type, $errors, $extraData);
    }
}
