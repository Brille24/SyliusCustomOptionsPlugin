<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;


use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;

class CustomerOptionPriceByExampleImporter implements CustomerOptionPriceByExampleImporterInterface
{
    protected const BATCH_SIZE = 100;

    /** @var CustomerOptionPriceUpdaterInterface */
    protected $priceUpdater;

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager
    ) {
        $this->priceUpdater  = $priceUpdater;
        $this->entityManager = $entityManager;
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

        $failed = 0;
        $i = 0;
        foreach ($productCodes as $productCode) {
            try {
                $price = $this->priceUpdater->updateForProduct(
                    $customerOptionCode,
                    $customerOptionValueCode,
                    $channelCode,
                    $productCode,
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
                $failed++;
            }
        }

        $this->entityManager->flush();

        return ['imported' => $i, 'failed' => $failed];
    }
}
