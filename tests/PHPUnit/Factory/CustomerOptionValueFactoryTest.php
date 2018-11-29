<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConfigurationException;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValueFactory;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactory;
use PHPUnit\Framework\TestCase;

class CustomerOptionValueFactoryTest extends TestCase
{
    /** @var CustomerOptionValueFactory */
    private $customerOptionValueFactory;

    protected function setUp()
    {
        $customerOptionPriceFactory = self::createMock(CustomerOptionValuePriceFactory::class);
        $customerOptionPriceFactory->method('createFromConfig')->willReturnCallback(function ($config) {
            return self::createMock(CustomerOptionValuePrice::class);
        });

        $this->customerOptionValueFactory = new CustomerOptionValueFactory($customerOptionPriceFactory);
    }

    /**
     * @dataProvider dataValidateWithInvalidConfiguration
     *
     * @throws ConfigurationException
     *
     * @param array $configuration
     * @param string $exceptionMessage
     */
    public function testValidateWithInvalidConfiguration(array $configuration, string $exceptionMessage): void
    {
        self::expectException(ConfigurationException::class);
        self::expectExceptionMessage($exceptionMessage);

        $this->customerOptionValueFactory->validateConfiguration($configuration);
    }

    public function dataValidateWithInvalidConfiguration(): array
    {
        return [
            'no code' => [
                [],
                'The configuration does not contain key: "code"',
            ],
            'no translations' => [
                ['code' => 'something'],
                'The configuration does not contain key: "translations"',
            ],
            'translations string' => [
                ['code' => 'something', 'translations' => 'hello'],
                'The translations have to be an array',
            ],
            'Not enough translations' => [
                ['code' => 'something', 'translations' => []],
                'The array has to be at least 1 element(s) long',
            ],
            'Missing prices key' => [
                ['code' => 'something', 'translations' => ['en' => 'something']],
                'The configuration does not contain key: "prices"',
            ],
            'Prices not array' => [
                ['code' => 'something', 'translations' => ['en' => 'something'], 'prices' => 'lll'],
                'The translations have to be an array',
            ],
        ];
    }

    public function testCreate(): void
    {
        $config = ['code' => 'something', 'translations' => ['en' => 'something'], 'prices' => [['20 EUR']]];

        $valueObject = $this->customerOptionValueFactory->createFromConfig($config);

        self::assertInstanceOf(CustomerOptionValueInterface::class, $valueObject);
        self::assertEquals('something', $valueObject->getName());
        self::assertEquals(1, $valueObject->getPrices()->count());
    }

    /** @dataProvider dataGenerateRandomConfiguration */
    public function testGenerateRandomConfiguration(int $amount): void
    {
        $configuration = $this->customerOptionValueFactory->generateRandomConfiguration($amount);

        self::assertEquals($amount, count($configuration));
        foreach ($configuration as $config) {
            $this->customerOptionValueFactory->validateConfiguration($config);
            self::assertArrayHasKey('en_US', $config['translations']);
        }
    }

    public function dataGenerateRandomConfiguration(): array
    {
        return ['one' => [1], 'many' => [5]];
    }
}
