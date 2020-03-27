<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Exceptions;


use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ConstraintViolationException extends \Exception
{
    /** @var ConstraintViolationListInterface */
    protected $violations;

    public function __construct(ConstraintViolationListInterface $violations, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->violations = $violations;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
