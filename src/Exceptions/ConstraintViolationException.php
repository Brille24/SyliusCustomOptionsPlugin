<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ConstraintViolationException extends \Exception
{
    /** @var ConstraintViolationListInterface */
    protected $violations;

    public function __construct(
        ConstraintViolationListInterface $violations,
        string $message = '',
        int $code = 0,
        Throwable $previous = null,
    ) {
        $this->violations = $violations;

        parent::__construct($message, $code, $previous);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
