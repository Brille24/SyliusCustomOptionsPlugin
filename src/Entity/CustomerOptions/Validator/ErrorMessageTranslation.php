<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Sylius\Component\Resource\Model\AbstractTranslation;

class ErrorMessageTranslation extends AbstractTranslation implements ErrorMessageTranslationInterface
{
    private $id;

    private $message;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getId()
    {
        return $this->id;
    }
}
