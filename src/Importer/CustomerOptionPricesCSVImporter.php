<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomerOptionPricesCSVImporter implements CustomerOptionPricesImporterInterface
{
    public const BATCH_SIZE = 10;

    private const REQUIRED_FIELDS = [
        'customer_option_code'       => true,
        'customer_option_value_code' => true,
        'channel_code'               => true,
        'valid_from'                 => false,
        'valid_to'                   => false,
        'type'                       => true,
        'amount'                     => true,
        'percent'                    => true,
        'product_code'               => true,
    ];

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var CustomerOptionPriceUpdaterInterface */
    protected $priceUpdater;

    /** @var SenderInterface */
    protected $sender;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        SenderInterface $sender,
        TokenStorageInterface $tokenStorage
    ) {
        $this->priceUpdater  = $priceUpdater;
        $this->entityManager = $entityManager;
        $this->sender        = $sender;
        $this->tokenStorage  = $tokenStorage;
    }

    /** {@inheritdoc} */
    public function importCustomerOptionPrices(string $source): array
    {
        $csv = $this->readCsv($source);

        // Handle updates
        $i = 0;
        $failed = [];
        foreach ($csv as $lineNumber => $data) {
            if (!$this->isRowValid($data)) {
                $failed[$lineNumber] = ['data' => $data, 'message' => 'Data is invalid'];

                continue;
            }

            try {
                $price = $this->priceUpdater->updateForProduct(
                    $data['customer_option_code'],
                    $data['customer_option_value_code'],
                    $data['channel_code'],
                    $data['product_code'],
                    $data['valid_from'],
                    $data['valid_to'],
                    $data['type'],
                    (int)$data['amount'],
                    (float)$data['percent']
                );

                $this->entityManager->persist($price);

                if (++$i % self::BATCH_SIZE === 0) {
                    $this->entityManager->flush();
                }
            } catch (\Throwable $exception) {
                $failed[$lineNumber] = ['data' => $data, 'message' => $exception->getMessage()];
            }
        }

        $this->entityManager->flush();

        $this->sendFailReport($failed);

        return ['imported' => $i, 'failed' => count($failed)];
    }

    private function sendFailReport(array $failed): void
    {
        // Send mail about failed imports
        /** @var AdminUserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $email = $user->getEmail();

        $csvHeader = ['Line', 'Error'];
        foreach (array_keys(current($failed)['data']) as $key) {
            $csvHeader[] = $key;
        }
        $csvData = [
            implode(',', $csvHeader),
        ];

        foreach ($failed as $line => $error) {
            $csvData[] = sprintf('%s,%s,%s', $line, $error['message'], implode(',', $error['data']));
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'cop');
        $csvPath = $tmpPath.'.csv';

        rename($tmpPath, $csvPath);
        file_put_contents($csvPath, implode("\n", $csvData));

        $this->sender->send('brille24_failed_price_import', [$email], ['failed' => $failed], [$csvPath]);
    }

    /**
     * @param string $source
     *
     * @return array
     */
    private function readCsv(string $source): array
    {
        $file = fopen($source, 'rb');

        $header = null;
        $csv    = [];
        $currentLineNumber = 0;
        while ($row = fgetcsv($file)) {
            $currentLineNumber++;

            // Use the first row as array keys
            if (null === $header) {
                $header = $row;

                continue;
            }

            // Replace empty strings with null
            $row = array_map(static function($value) {
                return '' !== $value ? $value : null;
            }, $row);

            $csv[$currentLineNumber] = array_combine($header, $row);
        }

        return $csv;
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    private function isRowValid(array $row): bool
    {
        // Check if all expected keys exist
        foreach (self::REQUIRED_FIELDS as $field => $valueRequired) {
            if (!array_key_exists($field, $row)) {
                return false;
            }

            if ($valueRequired && null === $row[$field]) {
                return false;
            }
        }

        return true;
    }
}
