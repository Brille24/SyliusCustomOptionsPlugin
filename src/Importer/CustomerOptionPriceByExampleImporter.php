<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Handler\ImportErrorHandlerInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

class CustomerOptionPriceByExampleImporter implements CustomerOptionPriceByExampleImporterInterface
{
    protected const BATCH_SIZE = 100;

    /** @var CustomerOptionPriceUpdaterInterface */
    protected $priceUpdater;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var ImportErrorHandlerInterface */
    protected $importErrorHandler;

    public function __construct(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ImportErrorHandlerInterface $importErrorHandler
    ) {
        $this->priceUpdater       = $priceUpdater;
        $this->entityManager      = $entityManager;
        $this->productRepository  = $productRepository;
        $this->importErrorHandler = $importErrorHandler;
    }

    /** {@inheritdoc} */
    public function importForProducts(array $productCodes, CustomerOptionValuePriceInterface $examplePrice): array
    {
        $dateFrom = null;
        $dateTo   = null;
        if ($examplePrice->getDateValid() !== null) {
            $dateFrom = $examplePrice->getDateValid()->getStart()->format(DATE_ATOM);
            $dateTo   = $examplePrice->getDateValid()->getEnd()->format(DATE_ATOM);
        }

        $customerOptionCode      = $examplePrice->getCustomerOptionValue()->getCustomerOption()->getCode();
        $customerOptionValueCode = $examplePrice->getCustomerOptionValue()->getCode();
        $channelCode             = $examplePrice->getChannel()->getCode();

        $type    = $examplePrice->getType();
        $amount  = $examplePrice->getAmount();
        $percent = $examplePrice->getPercent();

        $errors = [];
        $i      = 0;
        foreach ($productCodes as $productCode) {
            try {
                /** @var ProductInterface|null $product */
                $product = $this->productRepository->findOneByCode($productCode);
                Assert::isInstanceOf(
                    $product,
                    ProductInterface::class,
                    sprintf('Product with code "%s" not found', $productCode)
                );

                $price = $this->priceUpdater->updateForProduct(
                    $customerOptionCode,
                    $customerOptionValueCode,
                    $channelCode,
                    $product,
                    $dateFrom,
                    $dateTo,
                    $type,
                    $amount,
                    $percent
                );

                $this->entityManager->persist($price);

                if (++$i % self::BATCH_SIZE === 0) {
                    $this->entityManager->flush();
                }
            } catch (\Throwable $exception) {
                $errors[$productCode] = ['data' => $examplePrice, 'message' => $exception->getMessage()];
            }
        }

        $this->entityManager->flush();

        $this->importErrorHandler->handleErrors($errors);

        return ['imported' => $i, 'failed' => count($errors)];
    }
}
