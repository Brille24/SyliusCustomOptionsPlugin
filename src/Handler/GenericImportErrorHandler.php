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

    public function __construct(
        SenderInterface $sender,
        TokenStorageInterface $tokenStorage
    ) {
        $this->sender       = $sender;
        $this->tokenStorage = $tokenStorage;
    }

    /** {@inheritdoc} */
    public function handleErrors(string $type, array $errors, array $extraData): void
    {
        if (0 === count($errors)) {
            return;
        }

        // Send mail about failed imports
        /** @var AdminUserInterface $user */
        $user  = $this->tokenStorage->getToken()->getUser();
        $email = $user->getEmail();

        $this->sender->send(
            'brille24_failed_price_import_'.$type,
            [$email],
            ['failed' => $errors, 'extraData' => $extraData]
        );
    }
}
