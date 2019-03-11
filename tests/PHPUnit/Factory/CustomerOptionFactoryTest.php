<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConfigurationException;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionFactory;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValueFactory;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionGroupRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CustomerOptionFactoryTest extends TestCase
{
    /**
     * @var CustomerOptionFactory
     */
    private $customerOptionFactory;

    /** @var CustomerOptionGroupRepositoryInterface[] */
    private $customerOptionGroupRepository = [];

    public function setUp()
    {
        $customerOptionValueFactory = self::createMock(CustomerOptionValueFactory::class);

        $customerOptionGroupRepository = self::createMock(CustomerOptionGroupRepositoryInterface::class);
        $customerOptionGroupRepository->method('findAll')->willReturnCallback(
            function () {
                return $this->customerOptionGroupRepository;
            }
        );
        $customerOptionGroupRepository->method('findOneBy')->willReturnCallback(function (array $config) {
            $code = $config['code'];
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
            'missing code' => [
                [],
                'The configuration does not contain key: "code"',
            ],
            'missing translations' => [
                ['code' => 'something'],
                'The configuration does not contain key: "translations"',
            ],
            'no translations' => [
                ['code' => 'something', 'translations' => []],
                'The array has to be at least 1 element(s) long',
            ],
            'type missing' => [
                ['code' => 'something', 'translations' => ['en']],
                'The configuration does not contain key: "type"',
            ],
            'type invalid' => [
                ['code' => 'something', 'translations' => ['en'], 'type' => 'something'],
                '\'something\' should be in array file,text,select,multi_select,date,datetime,number,boolean',
            ],
            'select missing values' => [
                ['code' => 'something', 'translations' => ['en'], 'type' => CustomerOptionTypeEnum::MULTI_SELECT],
                'The configuration does not contain key: "values"',
            ],
            'missing group' => [
                ['code' => 'something', 'translations' => ['en'], 'type' => CustomerOptionTypeEnum::BOOLEAN],
                'The configuration does not contain key: "groups"',
            ],
        ];
    }

    /**
     * @dataProvider dataCreateWithSelect
     *
     * @throws \Exception
     *
     * @param array $config
     * @param int $configCount
     * @param bool $required
     */
    public function testCreateWithSelect(array $config, int $configCount, bool $required): void
    {
        $customerOption = $this->customerOptionFactory->createFromConfig($config);

        self::assertInstanceOf(CustomerOptionInterface::class, $customerOption);
        self::assertCount($configCount, $customerOption->getValues());
        self::assertEquals($required, $customerOption->isRequired());
    }

    public function dataCreateWithSelect(): array
    {
        return [
            'empty object' => [
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
            'empty object required' => [
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
            'type'         => CustomerOptionTypeEnum::TEXT,
            'groups'       => [],
            'required'     => true,
        ];

        $customerOption = $this->customerOptionFactory->createFromConfig($option);

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
            'type'         => CustomerOptionTypeEnum::NUMBER,
            'groups'       => ['en_US'],
            'required'     => true,
        ];

        $customerOption = $this->customerOptionFactory->createFromConfig($option);

        self::assertEquals(1, $customerOption->getGroupAssociations()->count());
        self::assertEquals('hello', $associated);
    }
}
