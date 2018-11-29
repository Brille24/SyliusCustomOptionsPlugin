<?php

declare(strict_types=1);

namespace Test\Brille24\SyliusCustomerOptionsPlugin\Entity;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use PHPUnit\Framework\TestCase;

class CustomerOptionTest extends TestCase
{
    /** @var CustomerOptionInterface */
    private $customerOption;

    public function setUp(): void
    {
        $this->customerOption = new CustomerOption();
    }

    public function testDefaultValues(): void
    {
        self::assertEquals(CustomerOptionTypeEnum::SELECT, $this->customerOption->getType());
        self::assertEquals(0, $this->customerOption->getValues()->count());
        self::assertEquals(0, $this->customerOption->getGroupAssociations()->count());
        self::assertEquals([], $this->customerOption->getConfiguration());
    }

    /** @dataProvider dataSetTypeToSelect */
    public function testSetTypeToSelect(string $type, array $configuration): void
    {
        $this->customerOption->setType($type);

        self::assertEquals($type, $this->customerOption->getType());
        self::assertEquals($configuration, $this->customerOption->getConfiguration());
    }

    public function dataSetTypeToSelect(): array
    {
        return [
            'select'       => [CustomerOptionTypeEnum::SELECT, []],
            'multi-select' => [CustomerOptionTypeEnum::MULTI_SELECT, []],
            'boolean'      => [
                CustomerOptionTypeEnum::BOOLEAN,
                CustomerOptionTypeEnum::getConfigurationArray()['boolean'],
            ],
            'number' => [
                CustomerOptionTypeEnum::NUMBER,
                CustomerOptionTypeEnum::getConfigurationArray()['number'],
            ],
        ];
    }

    public function testInvalidType(): void
    {
        self::expectException(\Exception::class);
        self::expectExceptionMessage('Invalid type');

        $this->customerOption->setType('Hello');
    }

    public function testAddValues()
    {
        // Setup
        $value = self::createMock(CustomerOptionValueInterface::class);

        // Execute
        $this->customerOption->addValue($value);

        // Assert
        $result = $this->customerOption->getValues();
        self::assertEquals(1, $result->count());
        self::assertContains($value, $result);
    }

    public function testRemoveValues()
    {
        // Setup
        $value = self::createMock(CustomerOptionValueInterface::class);
        $this->customerOption->setValues([$value]);

        // Execute
        $this->customerOption->removeValue($value);

        // Assert
        $result = $this->customerOption->getValues();
        self::assertEquals(0, $result->count());
        self::assertNotContains($value, $result);
    }
}
