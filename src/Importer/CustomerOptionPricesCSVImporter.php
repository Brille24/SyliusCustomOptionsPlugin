<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;
use function Clue\StreamFilter\fun;

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

    public function __construct(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager
    ) {
        $this->priceUpdater  = $priceUpdater;
        $this->entityManager = $entityManager;
    }

    /** {@inheritdoc} */
    public function importCustomerOptionPrices(string $source): void
    {
        $csv = $this->readCsv($source);

        // Handle updates
        $i = 0;
        foreach ($csv as $row) {
            if (!$this->isRowValid($row)) {
                // Log error

                continue;
            }

            // No product code means it's a global pricing
            $price = $this->priceUpdater->updateForProduct(
                $row['customer_option_code'],
                $row['customer_option_value_code'],
                $row['channel_code'],
                $row['product_code'],
                $row['valid_from'],
                $row['valid_to'],
                $row['type'],
                (int)$row['amount'],
                (float)$row['percent']
            );

            $this->entityManager->persist($price);

            if (++$i % self::BATCH_SIZE === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();
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
        while ($row = fgetcsv($file)) {
            // Use the first row as array keys
            if (null === $header) {
                $header = $row;

                continue;
            }

            // Replace empty strings with null
            $row = array_map(static function($value) {
                return '' !== $value ? $value : null;
            }, $row);

            $csv[] = array_combine($header, $row);
        }

        return $csv;
    }

    /**
     * @param array $csv
     */
    private function validateCsv(array $csv): void
    {
        foreach ($csv as $row) {
            Assert::isArray($row);
            Assert::true($this->isRowValid($row));
        }
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
