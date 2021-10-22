<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GenericImportErrorHandler implements ImportErrorHandlerInterface
{
    /** @var SenderInterface */
    protected $sender;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var string */
    protected $email_code;

    public function __construct(SenderInterface $sender, TokenStorageInterface $tokenStorage, string $email_code)
    {
        $this->sender = $sender;
        $this->tokenStorage = $tokenStorage;
        $this->email_code = $email_code;
    }

    /** {@inheritdoc} */
    public function handleErrors(array $errors, array $extraData): void
    {
        if (0 === count($errors)) {
            return;
        }

        // Send mail about failed imports
        /** @var AdminUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $email = $user->getEmail();

        $this->sender->send($this->email_code, [$email], ['errors' => $errors, 'extraData' => $extraData]);
    }
}
