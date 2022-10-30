<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Sylius\Component\Resource\Model\AbstractTranslation;

class ErrorMessageTranslation extends AbstractTranslation implements ErrorMessageTranslationInterface
{
    protected ?int $id;

    protected ?string $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
