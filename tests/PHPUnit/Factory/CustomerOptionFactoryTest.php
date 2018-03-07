<?php

declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Factory\CustomerOptionFactory;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerOptionFactoryTest extends TestCase
{
    /**
     * @var CustomerOptionFactory
     */
    private $customerOptionFactory;

    public function setUp()
    {
        $groupRepositoryMock = $this->createMock(CustomerOptionGroupRepositoryInterface::class);
        $groupRepositoryMock->method('findAll')->willReturn([]);

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('en_US');

        $channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $channelRepositoryMock->method('findAll')->willReturn([$channel]);
        $channelRepositoryMock->method('findOneByCode')->willReturnCallback(function (string $code) use ($channel) {
            if ($code === 'en_US') {
                return $channel;
            }
            return null;
        });

        $this->customerOptionFactory = new CustomerOptionFactory(
            $groupRepositoryMock,
            $channelRepositoryMock
        );
    }

    public function assertCustomerOption(CustomerOptionInterface $customerOption): void
    {
        /** @var CustomerOption $customerOption */
        $this->assertInstanceOf(CustomerOptionInterface::class, $customerOption);
        $this->assertNotNull($customerOption->getCode());
        $this->assertArrayHasKey('en_US', $customerOption->getTranslations()->toArray());
    }

    /**
     * @test
     */
    public function testGenerateRandom()
    {
        $amount = 5;

        $customerOptions = $this->customerOptionFactory->generateRandom($amount);

        $this->assertEquals($amount, count($customerOptions));
        foreach ($customerOptions as $customerOption) {
            $this->assertCustomerOption($customerOption);
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function testCreateWithValidOptions()
    {
        $options = [
            'code'         => 'some_option',
            'translations' => [
                'en_US' => 'Some Option',
            ],
            'type'         => 'text',
            'required'     => false,
        ];

        $customerOption = null;

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertNotNull($customerOption);
        $this->assertInstanceOf(CustomerOption::class, $customerOption);
        $this->assertCustomerOption($customerOption);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithoutTranslation()
    {
        $options = [
            'code' => 'some_option',
            'type' => 'select',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('At least one translation is required.');

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertNull($customerOption);
        $this->assertCustomerOption($customerOption);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithInvalidType()
    {
        $options = [
            'code'         => 'some_option',
            'translations' => [
                'en_US' => 'Some Option',
            ],
            'type'         => 'abc',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Customer Option Type "abc" is not valid.');

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertNull($customerOption);
        $this->assertCustomerOption($customerOption);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithOptionValues()
    {
        $options = [
            'code'         => 'some_option',
            'translations' => [
                'en_US' => 'Some Option',
            ],
            'type'         => 'select',
            'values'       => [
                [
                    'code'         => 'val_1',
                    'translations' => [
                        'en_US' => 'Value 1',
                    ],
                    'prices'       => [],
                ],
                [
                    'code'         => 'val_2',
                    'translations' => [
                        'en_US' => 'Value 2',
                    ],
                    'prices'       => [],
                ],
            ],
        ];

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertCount(2, $customerOption->getValues());
        $this->assertEquals('val_1', $customerOption->getValues()[0]->getCode());
        $this->assertEquals('val_2', $customerOption->getValues()[1]->getCode());
        $this->assertCustomerOption($customerOption);
    }
}
