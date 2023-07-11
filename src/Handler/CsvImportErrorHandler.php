<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Handler;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CsvImportErrorHandler implements ImportErrorHandlerInterface
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
        $csvPath = $this->buildCsv($errors);

        $this->sender->send(
            $this->emailCode,
            [$email],
            ['errors' => $errors, 'extraData' => $extraData],
            [$csvPath],
        );
    }

    protected function buildCsv(array $errors): string
    {
        // Build csv to attach to the email
        $csvHeader = ['Error'];
        foreach (array_keys(current(current($errors))['data']) as $key) {
            $csvHeader[] = $key;
        }
        $csvData = [
            implode(',', $csvHeader),
        ];

        foreach ($errors as $productCode => $productErrors) {
            foreach ($productErrors as $error) {
                $csvData[] = sprintf('"%s",%s', $error['message'], implode(',', $error['data']));
            }
        }

        /** @var string $tmpPath */
        $tmpPath = tempnam(sys_get_temp_dir(), 'cop');
        $csvPath = $tmpPath . '.csv';

        rename($tmpPath, $csvPath);
        file_put_contents($csvPath, implode("\n", $csvData));

        return $csvPath;
    }
}
