<?php

declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Exceptions\ConfigurationException;
use Brille24\CustomerOptionsPlugin\Factory\CustomerOptionFactory;
use Brille24\CustomerOptionsPlugin\Factory\CustomerOptionValueFactory;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CustomerOptionFactoryTest extends TestCase
{
    /**
     * @var CustomerOptionFactory
     */
    private $customerOptionFactory;

    /** @var CustomerOptionGroupRepositoryInterface */
    private $customerOptionGroupRepository = [];

    public function setUp()
    {
        $customerOptionValueFactory = self::createMock(CustomerOptionValueFactory::class);

        $customerOptionGroupRepository = self::createMock(CustomerOptionGroupRepositoryInterface::class);
        $customerOptionGroupRepository->method('findAll')->willReturnCallback(
            function () { return $this->customerOptionGroupRepository; }
        );
        $customerOptionGroupRepository->method('findOneByCode')->willReturnCallback(function (string $code) {
            foreach ($this->customerOptionGroupRepository as $group) {
                if ($group->getCode() === $code) {
                    return $group;
                }
            }
            return null;
        });

        $this->customerOptionFactory = new CustomerOptionFactory(
            $customerOptionGroupRepository,
            $customerOptionValueFactory
        );
    }

    /** @dataProvider dataValidateInvalidConfiguration */
    public function testValidateWithInvalidConfiguration(array $configuration, string $errorMessage): void
    {
        self::expectException(ConfigurationException::class);
        self::expectExceptionMessage($errorMessage);

        $this->customerOptionFactory->validateConfiguration($configuration);
    }

    public function dataValidateInvalidConfiguration(): array
    {
        return [
            'missing code'          => [
                [],
                'The configuration does not contain key: "code"',
            ],
            'missing translations'  => [
                ['code' => 'something'],
                'The configuration does not contain key: "translations"',
            ],
            'no translations'       => [
                ['code' => 'something', 'translations' => []],
                'The array has to be at least 1 element(s) long',
            ],
            'type missing'          => [
                ['code' => 'something', 'translations' => ['en']],
                'The configuration does not contain key: "type"',
            ],
            'type invalid'          => [
                ['code' => 'something', 'translations' => ['en'], 'type' => 'something'],
                '\'something\' should be in array text,select,multi_select,file,date,datetime,number,boolean',
            ],
            'select missing values' => [
                ['code' => 'something', 'translations' => ['en'], 'type' => 'multi_select'],
                'The configuration does not contain key: "values"',
            ],
            'missing group'         => [
                ['code' => 'something', 'translations' => ['en'], 'type' => 'file'],
                'The configuration does not contain key: "groups"',
            ],
        ];
    }

    /**
     * @dataProvider dataCreateWithSelect
     * @throws \Exception
     */
    public function testCreateWithSelect(array $config, int $configCount, bool $required): void
    {
        $customerOption = $this->customerOptionFactory->create($config);

        self::assertInstanceOf(CustomerOptionInterface::class, $customerOption);
        self::assertCount($configCount, $customerOption->getValues());
        self::assertEquals($required, $customerOption->isRequired());
    }

    public function dataCreateWithSelect(): array
    {
        return [
            'empty object'           => [
                [
                    'code'         => 'something',
                    'translations' => ['de_DE' => 'Etwas'],
                    'type'         => 'select',
                    'values'       => [],
                    'groups'       => [],
                ],
                0,
                false,
            ],
            'empty object required'  => [
                [
                    'code'         => 'something',
                    'translations' => ['de_DE' => 'Etwas'],
                    'type'         => 'select',
                    'values'       => [],
                    'groups'       => [],
                    'required'     => true,
                ],
                0,
                true,
            ],
            'values object required' => [
                [
                    'code'         => 'something',
                    'translations' => ['de_DE' => 'Etwas'],
                    'type'         => 'select',
                    'values'       => [[], []],
                    'groups'       => [],
                    'required'     => true,
                ],
                2,
                true,
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    public function testCreateWithConfiguredOptions(): void
    {
        $option = [
            'code'         => 'something',
            'translations' => ['de_DE' => 'Etwas'],
            'type'         => 'file',
            'groups'       => [],
            'required'     => true,
        ];

        $customerOption = $this->customerOptionFactory->create($option);

        self::assertInstanceOf(CustomerOptionInterface::class, $customerOption);
        self::assertEquals(true, $customerOption->isRequired());
    }

    /**
     * @dataProvider dataGenerateConfiguration
     *
     * @param int $count
     *
     * @throws \Exception
     */
    public function testGenerateRandomConfiguration(int $count): void
    {
        $configuration = $this->customerOptionFactory->generateRandomConfiguration($count);

        self::assertCount($count, $configuration);
        foreach ($configuration as $config) {
            $this->customerOptionFactory->validateConfiguration($config);
        }
    }

    public function dataGenerateConfiguration(): array
    {
        return ['one' => [1], 'many' => [5]];
    }

    public function testGroupAssociation(): void
    {
        $associated = null;
        $group = self::createConfiguredMock(CustomerOptionGroupInterface::class, ['getCode' => 'en_US']);
        $group->method('addOptionAssociation')->willReturnCallback(function (CustomerOptionAssociation $option) use (&$associated) {
            $associated = 'hello';
        });
        $this->customerOptionGroupRepository = [$group];

        $option = [
            'code'         => 'something',
            'translations' => ['de_DE' => 'Etwas'],
            'type'         => 'file',
            'groups'       => ['en_US'],
            'required'     => true,
        ];

        $customerOption = $this->customerOptionFactory->create($option);

        self::assertEquals(1, $customerOption->getGroupAssociations()->count());
        self::assertEquals('hello', $associated);
    }
}
