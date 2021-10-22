<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ErrorMessageTranslationInterface extends ResourceInterface, TranslationInterface
{
    /**
     * @return string
     */
    public function getMessage(): ?string;

    public function setMessage(string $message): void;
}
