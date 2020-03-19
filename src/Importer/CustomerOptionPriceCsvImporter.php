<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Handler\ImportErrorHandlerInterface;
use Brille24\SyliusCustomerOptionsPlugin\Reader\CsvReaderInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

class CustomerOptionPriceCsvImporter implements CustomerOptionPriceCsvImporterInterface
{
    protected const BATCH_SIZE = 100;

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

    /** @var CsvReaderInterface */
    private $csvReader;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var CustomerOptionPriceUpdaterInterface */
    protected $priceUpdater;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var ImportErrorHandlerInterface */
    protected $importErrorHandler;

    /** @var ProductInterface[] */
    protected $products = [];

    public function __construct(
        CsvReaderInterface $csvReader,
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ImportErrorHandlerInterface $importErrorHandler
    ) {
        $this->csvReader          = $csvReader;
        $this->priceUpdater       = $priceUpdater;
        $this->entityManager      = $entityManager;
        $this->productRepository  = $productRepository;
        $this->importErrorHandler = $importErrorHandler;
    }

    /** {@inheritdoc} */
    public function import(string $source): array
    {
        $csv = $this->csvReader->readCsv($source);

        // Handle updates
        $i      = 0;
        $errors = [];
        foreach ($csv as $lineNumber => $data) {
            if (!$this->csvReader->isRowValid($data, self::REQUIRED_FIELDS)) {
                $errors[$lineNumber] = ['data' => $data, 'message' => 'Data is invalid'];

                continue;
            }

            try {
                $product = $this->getProduct($data['product_code']);
                Assert::isInstanceOf(
                    $product,
                    ProductInterface::class,
                    sprintf('Product with code "%s" not found', $data['product_code'])
                );

                $price = $this->priceUpdater->updateForProduct(
                    $data['customer_option_code'],
                    $data['customer_option_value_code'],
                    $data['channel_code'],
                    $product,
                    $data['valid_from'],
                    $data['valid_to'],
                    $data['type'],
                    (int) $data['amount'],
                    (float) $data['percent']
                );

                // Add the value price to the product so we can use it in later validations.
                $product->addCustomerOptionValuePrice($price);

                $this->entityManager->persist($price);

                if (++$i % self::BATCH_SIZE === 0) {
                    $this->entityManager->flush();
                }
            } catch (\Throwable $exception) {
                $errors[$lineNumber] = ['data' => $data, 'message' => $exception->getMessage()];
            }
        }

        $this->entityManager->flush();

        $this->importErrorHandler->handleErrors($errors);

        return ['imported' => $i, 'failed' => count($errors)];
    }

    /**
     * @param string $code
     *
     * @return ProductInterface|null
     */
    private function getProduct(string $code): ?ProductInterface
    {
        if (!isset($this->products[$code])) {
            $this->products[$code] = $this->productRepository->findOneByCode($code);
        }

        return $this->products[$code];
    }
}
