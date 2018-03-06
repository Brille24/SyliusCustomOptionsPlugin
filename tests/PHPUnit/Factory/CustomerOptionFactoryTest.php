<?php

declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Factory\CustomerOptionFactory;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

class CustomerOptionFactoryTest extends TestCase
{
    /**
     * @var CustomerOptionFactory
     */
    private $customerOptionFactory;

    public function setUp()
    {
        $groupRepositoryMock = $this->createMock(CustomerOptionGroupRepositoryInterface::class);
        $groupRepositoryMock->expects($this->any())->method('findAll')->willReturn([]);

        $channelRepositoryMock = $this->createMock(ChannelRepositoryInterface::class);
        $channelRepositoryMock->expects($this->any())->method('findAll')->willReturn([]);

        $this->customerOptionFactory = new CustomerOptionFactory(
            $this->createMock(EntityManagerInterface::class),
            $groupRepositoryMock,
            $channelRepositoryMock
        );
    }

    /**
     * @test
     */
    public function testGenerateRandom()
    {
        $amount = 5;

        $customerOptions = $this->customerOptionFactory->generateRandom($amount);

        $this->assertEquals($amount, count($customerOptions));
    }

    /**
     * @test
     */
    public function testCreateWithValidOptions()
    {
        $options = [
            'code' => 'some_option',
            'translations' => [
                'en_US' => 'Some Option',
            ],
            'type' => 'text',
            'required' => false,
        ];

        $customerOption = null;

        try {
            $customerOption = $this->customerOptionFactory->create($options);
        } catch (\Throwable $e) {
        }

        $this->assertNotNull($customerOption);
        $this->assertInstanceOf(CustomerOption::class, $customerOption);
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

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertNull($customerOption);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithInvalidType()
    {
        $options = [
            'code' => 'some_option',
            'translations' => [
                'en_US' => 'Some Option',
            ],
            'type' => 'abc',
        ];

        $this->expectException(\Exception::class);

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertNull($customerOption);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithOptionValues()
    {
        $options = [
            'code' => 'some_option',
            'translations' => [
                'en_US' => 'Some Option',
            ],
            'type' => 'select',
            'values' => [
                [
                    'code' => 'val_1',
                    'translations' => [
                        'en_US' => 'Value 1',
                    ],
                    'prices' => [],
                ],
                [
                    'code' => 'val_2',
                    'translations' => [
                        'en_US' => 'Value 2',
                    ],
                    'prices' => [],
                ],
            ],
        ];

        $customerOption = $this->customerOptionFactory->create($options);

        $this->assertCount(2, $customerOption->getValues());
        $this->assertEquals('val_1', $customerOption->getValues()[0]->getCode());
        $this->assertEquals('val_2', $customerOption->getValues()[1]->getCode());
    }
}
