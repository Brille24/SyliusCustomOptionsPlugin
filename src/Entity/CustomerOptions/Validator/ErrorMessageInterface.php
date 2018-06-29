<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface ErrorMessageInterface extends ResourceInterface, TranslatableInterface
{
    public function getValidator(): ValidatorInterface;

    public function setValidator(ValidatorInterface $validator): void;

    public function getMessage(): ?string;

    public function setMessage(string $message): void;
}
