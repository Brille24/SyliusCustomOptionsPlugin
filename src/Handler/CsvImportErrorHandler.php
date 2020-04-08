<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CsvImportErrorHandler implements ImportErrorHandlerInterface
{
    /** @var SenderInterface */
    protected $sender;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var string */
    protected $email_code;

    public function __construct(
        SenderInterface $sender,
        TokenStorageInterface $tokenStorage,
        string $email_code
    ) {
        $this->sender       = $sender;
        $this->tokenStorage = $tokenStorage;
        $this->email_code   = $email_code;
    }

    /** {@inheritdoc} */
    public function handleErrors(array $errors, array $extraData): void
    {
        if (0 === count($errors)) {
            return;
        }

        // Send mail about failed imports
        /** @var AdminUserInterface $user */
        $user    = $this->tokenStorage->getToken()->getUser();
        $email   = $user->getEmail();
        $csvPath = $this->buildCsv($errors);

        $this->sender->send(
            $this->email_code,
            [$email],
            ['errors' => $errors, 'extraData' => $extraData],
            [$csvPath]
        );
    }

    /**
     * @param array $errors
     *
     * @return string
     */
    protected function buildCsv(array $errors): string
    {
        // Build csv to attach to the email
        $csvHeader = ['Line', 'Error'];
        foreach (array_keys(current($errors)['data']) as $key) {
            $csvHeader[] = $key;
        }
        $csvData = [
            implode(',', $csvHeader),
        ];

        foreach ($errors as $line => $error) {
            $csvData[] = sprintf('%s,%s,%s', $line, $error['message'], implode(',', $error['data']));
        }

        /** @var string $tmpPath */
        $tmpPath = tempnam(sys_get_temp_dir(), 'cop');
        $csvPath = $tmpPath.'.csv';

        rename($tmpPath, $csvPath);
        file_put_contents($csvPath, implode("\n", $csvData));

        return $csvPath;
    }
}
