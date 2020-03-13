<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

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

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->priceUpdater  = $priceUpdater;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /** {@inheritdoc} */
    public function importCustomerOptionPrices(string $source): array
    {
        $csv = $this->readCsv($source);

        // Handle updates
        $i = 0;
        $failed = 0;
        foreach ($csv as $lineNumber => $data) {
            if (!$this->isRowValid($data)) {
                $failed++;

                // Log invalid data
                $this->logger->warning(sprintf('Line %s with data: [%s] is invalid', $lineNumber, implode(', ', $data)));

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
                $failed++;

                // Log exception
                $this->logger->error($exception->getMessage());
            }
        }

        $this->entityManager->flush();

        return ['imported' => $i, 'failed' => $failed];
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
