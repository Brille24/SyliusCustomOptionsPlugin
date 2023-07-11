<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConfigurationException;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactory;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerOptionValuePriceFactoryTest extends TestCase
{
    private \Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactory $customerOptionPriceFactory;

    private array $channelRepository = [];

    public function setUp(): void
    {
        $channelRepository = self::createMock(ChannelRepositoryInterface::class);
        $channelRepository->method('findAll')->willReturnCallback(fn () => $this->channelRepository);
        $channelRepository->method('findOneByCode')->willReturnCallback(fn ($code) => $this->channelRepository[$code] ?? null);

        $this->customerOptionPriceFactory = new CustomerOptionValuePriceFactory($channelRepository);
    }

    /**
     * @dataProvider dataValidateConfigurationInvalid
     *
     * @throws ConfigurationException
     */
    public function testValidateConfigurationInvalid(array $configuration, string $exceptionMessage)
    {
        self::expectException(ConfigurationException::class);
        self::expectExceptionMessage($exceptionMessage);

        $this->customerOptionPriceFactory->validateConfiguration($configuration);
    }

    public function dataValidateConfigurationInvalid(): array
    {
        return [
            'missing type' => [
                [],
                'The configuration does not contain key: "type"',
            ],
            'invalid type' => [
                ['type' => 'something'],
                '\'something\' should be in array fixed,percent',
            ],
            'valid type but missing price' => [
                ['type' => 'fixed'],
                'The configuration does not contain key: "amount"',
            ],
            'valid type but missing percent' => [
                ['type' => 'percent'],
                'The configuration does not contain key: "percent"',
            ],
            'missing channel' => [
                ['type' => 'fixed', 'amount' => 10],
                'The configuration does not contain key: "channel"',
            ],
        ];
    }

    public function testCreateWithMissingChannel(): void
    {
        self::expectException(EntityNotFoundException::class);
        self::expectExceptionMessage('Could not find Channel with code: "does_not_exist"');

        $configuration = [
            'type' => 'fixed',
            'amount' => 100,
            'channel' => 'does_not_exist',
        ];

        $this->customerOptionPriceFactory->createFromConfig($configuration);
    }

    public function testCreateSuccess(): void
    {
        $this->channelRepository = ['en_US' => self::createMock(ChannelInterface::class)];

        $configuration = [
            'type' => 'fixed',
            'amount' => 100,
            'channel' => 'en_US',
        ];

        $customerOptionValuePrice = $this->customerOptionPriceFactory->createFromConfig($configuration);

        self::assertInstanceOf(CustomerOptionValuePrice::class, $customerOptionValuePrice);
    }

    /** @dataProvider dataGenerateConfiguration */
    public function testGenerateRandomConfiguration(int $count): void
    {
        $this->channelRepository = [self::createConfiguredMock(ChannelInterface::class, ['getCode' => 'en_US'])];

        $randomElements = $this->customerOptionPriceFactory->generateRandomConfiguration($count);

        self::assertCount($count, $randomElements);
        foreach ($randomElements as $randomElement) {
            $this->customerOptionPriceFactory->validateConfiguration($randomElement);
            self::assertNotNull($randomElement['channel']);
        }
    }

    public function dataGenerateConfiguration(): array
    {
        return ['one' => [1], 'many' => [5]];
    }
}
