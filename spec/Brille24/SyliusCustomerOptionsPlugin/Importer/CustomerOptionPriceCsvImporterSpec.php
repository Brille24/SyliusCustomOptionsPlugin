<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Handler\ImportErrorHandlerInterface;
use Brille24\SyliusCustomerOptionsPlugin\Reader\CsvReaderInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CustomerOptionPriceCsvImporterSpec extends ObjectBehavior
{
    public function let(
        CsvReaderInterface $csvReader,
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ImportErrorHandlerInterface $importErrorHandler
    ): void {
        $this->beConstructedWith(
            $csvReader,
            $priceUpdater,
            $entityManager,
            $productRepository,
            $importErrorHandler
        );
    }

    public function it_updates_prices(
        CsvReaderInterface $csvReader,
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ImportErrorHandlerInterface $importErrorHandler
    ): void {
        $this->setupValidCsvReader($csvReader);

        $productRepository->findOneByCode(Argument::type('string'))->willReturn($product);

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldBeCalledTimes(2)->willReturn($valuePrice);

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            null,
            null,
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldBeCalledTimes(1)->willReturn($valuePrice);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldBeCalledTimes(3);
        $entityManager->flush()->shouldBeCalled();

        $importErrorHandler->handleErrors([])->shouldBeCalled();

        $this->import('some_path');
    }

    public function it_sends_mail_on_failed_import(
        CsvReaderInterface $csvReader,
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ImportErrorHandlerInterface $importErrorHandler
    ): void {
        $this->setupInValidCsvReader($csvReader);

        $productRepository->findOneByCode(Argument::type('string'))->willThrow(\InvalidArgumentException::class);

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldNotBeCalled();

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            null,
            null,
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldNotBeCalled();

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldNotBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $importErrorHandler->handleErrors(Argument::size(3))->shouldBeCalled();

        $this->import('some_path');
    }

    /**
     * @param CsvReaderInterface $csvReader
     */
    private function setupValidCsvReader(CsvReaderInterface $csvReader): void
    {
        $csvData = [
            [
                'product_code'               => 'tshirt',
                'customer_option_code'       => 'color',
                'customer_option_value_code' => 'red',
                'channel_code'               => 'US_WEB',
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 200,
                'percent'                    => 0.0,
                'valid_from'                 => null,
                'valid_to'                   => null,
            ],
            [
                'product_code'               => 'tshirt',
                'customer_option_code'       => 'color',
                'customer_option_value_code' => 'black',
                'channel_code'               => 'US_WEB',
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 200,
                'percent'                    => 0.0,
                'valid_from'                 => '2020-01-01',
                'valid_to'                   => '2020-02-31',
            ],
            [
                'product_code'               => 'mug',
                'customer_option_code'       => 'material',
                'customer_option_value_code' => 'wood',
                'channel_code'               => 'US_WEB',
                'type'                       => CustomerOptionValuePrice::TYPE_PERCENT,
                'amount'                     => 0,
                'percent'                    => 0.1,
                'valid_from'                 => '2020-01-01',
                'valid_to'                   => '2020-02-31',
            ],
        ];

        $csvReader->readCsv(Argument::type('string'))->willReturn($csvData);
        $csvReader->isRowValid(Argument::type('array'), Argument::type('array'))->willReturn(true);
    }

    /**
     * @param CsvReaderInterface $csvReader
     */
    private function setupInValidCsvReader(CsvReaderInterface $csvReader): void
    {
        $csvData = [
            [
                'product_code'               => 'tshir',
                'customer_option_code'       => 'color',
                'customer_option_value_code' => 'red',
                'channel_code'               => 'US_WEB',
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 200,
                'percent'                    => 0.0,
                'valid_fro'                  => null,
                'valid_to'                   => null,
            ],
            [
                'product_code'               => 'tshirt',
                'customer_option_code'       => 'color',
                'customer_option_value_code' => null,
                'channel_code'               => 'US_WEB',
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 200,
                'percent'                    => 0.0,
                'valid_fro'                  => '2020-01-01',
                'valid_to'                   => '2020-02-31',
            ],
            [
                'product_code'               => 'mug',
                'customer_option_code'       => 'material',
                'customer_option_value_code' => 'wood',
                'channel_code'               => 'US_WEB',
                'type'                       => CustomerOptionValuePrice::TYPE_PERCENT,
                'amount'                     => 0,
                'percent'                    => 0.1,
                'valid_fro'                  => '2020-01-01',
                'valid_to'                   => '2020-02-31',
            ],
        ];

        $csvReader->readCsv(Argument::type('string'))->willReturn($csvData);
        $csvReader->isRowValid(Argument::type('array'), Argument::type('array'))->willReturn(false);
    }
}
