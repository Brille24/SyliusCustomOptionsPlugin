<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GenericImportErrorHandler implements ImportErrorHandlerInterface
{
    public function __construct(
        protected SenderInterface $sender,
        protected TokenStorageInterface $tokenStorage,
        protected string $emailCode,
    )
    {
    }

    /** @inheritdoc */
    public function handleErrors(array $errors, array $extraData): void
    {
        if (0 === count($errors)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return;
        }

        // Send mail about failed imports
        /** @var AdminUserInterface $user */
        $user = $token->getUser();
        $email = $user->getEmail();

        $this->sender->send(
            $this->emailCode,
            [$email],
            ['errors' => $errors, 'extraData' => $extraData],
        );
    }
}
