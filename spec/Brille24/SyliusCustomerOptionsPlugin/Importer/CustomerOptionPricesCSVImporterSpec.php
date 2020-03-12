<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CustomerOptionPricesCSVImporterSpec extends ObjectBehavior
{
    public function let(CustomerOptionPriceUpdaterInterface $priceUpdater, EntityManagerInterface $entityManager): void
    {
        $this->beConstructedWith($priceUpdater, $entityManager);
    }

    public function it_updates_prices(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        CustomerOptionValuePriceInterface $valuePrice
    ): void {
        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
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
            Argument::type('string'),
            null,
            null,
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldBeCalledTimes(1)->willReturn($valuePrice);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldBeCalledTimes(3);
        $entityManager->flush()->shouldBeCalled();

        $this->importCustomerOptionPrices($this->getValidCsvFile());
    }

    /**
     * @return string
     */
    private function getValidCsvFile(): string
    {
        $csvString = <<<EOT
product_code,customer_option_code,customer_option_value_code,channel_code,type,amount,percent,valid_from,valid_to
first_product,lens_type,none,glasses24,FIXED_AMOUNT,1000,0.0,,
first_product,lens_type,none,glasses24,FIXED_AMOUNT,1500,0.0,2020-01-01,2020-02-31
second_product,lens_type,none,glasses24,FIXED_AMOUNT,1500,0.0,2020-01-01,2020-02-31
EOT;

        $filePath = @tempnam('customer_options', 'co');

        file_put_contents($filePath, $csvString);

        return $filePath;
    }
}
