<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Entity\Tools;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Condition;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ConditionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\ValidatorInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    /** @var ConditionInterface */
    private $condition;

    public function setUp()
    {
        $this->condition = new Condition();
    }

    public function testDefaultValues()
    {
        self::assertNull($this->condition->getCustomerOption());
        self::assertNull($this->condition->getComparator());
        self::assertNull($this->condition->getValue());
        self::assertNull($this->condition->getValidator());
    }

    /**
     * @test
     */
    public function testSetCustomerOption()
    {
        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::TEXT);

        $this->condition->setCustomerOption($customerOption);

        self::assertEquals($customerOption, $this->condition->getCustomerOption());
    }

    /**
     * @test
     * @dataProvider comparatorProvider
     *
     * @param string $comparator
     */
    public function testSetComparator(string $comparator)
    {
        $this->condition->setComparator($comparator);

        self::assertEquals($comparator, $this->condition->getComparator());
    }

    public function comparatorProvider()
    {
        $values = ConditionComparatorEnum::getConstList();

        foreach ($values as &$value) {
            $value = [$value];
        }

        return $values;
    }

    public function testSetValidator()
    {
        $validator = self::createMock(ValidatorInterface::class);

        $this->condition->setValidator($validator);

        self::assertEquals($validator, $this->condition->getValidator());
    }

    /**
     * @dataProvider valueProvider
     *
     * @param mixed $type
     * @param mixed $value
     */
    public function testSetValue($type, $value)
    {
        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getType')->willReturn($type);

        $this->condition->setCustomerOption($customerOption);
        $this->condition->setValue($value);

        $val = $this->condition->getValue();

        self::assertEquals($value, $val['value']);

        return $this->condition;
    }

    public function valueProvider()
    {
        return [
            [
                CustomerOptionTypeEnum::NUMBER,
                1234,
            ],
            [
                CustomerOptionTypeEnum::TEXT,
                'abcd',
            ],
            [
                CustomerOptionTypeEnum::SELECT,
                ['abc', 'def', 'ghi'],
            ],
            [
                CustomerOptionTypeEnum::DATE,
                new \DateTime(),
            ],
            [
                CustomerOptionTypeEnum::BOOLEAN,
                true,
            ],
        ];
    }

    /**
     * @dataProvider compareProvider
     *
     * @param mixed $type
     * @param mixed $value
     * @param mixed $comparators
     * @param mixed $testValues
     */
    public function testIsMet($type, $value, $comparators, $testValues)
    {
        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getType')->willReturn($type);

        $this->condition->setCustomerOption($customerOption);
        $this->condition->setValue($value);

        foreach ($comparators as $comparator) {
            $this->condition->setComparator($comparator);

            foreach ($testValues as $testValue) {
                $actual = $this->condition->isMet($testValue);

                if ($type === CustomerOptionTypeEnum::TEXT) {
                    $testValue = strlen($testValue);
                } elseif (CustomerOptionTypeEnum::isDate($type)) {
                    if ($type === CustomerOptionTypeEnum::DATETIME) {
                        $date   = $testValue['date'];
                        $time   = $testValue['time'];
                        $newVal = new \DateTime(
                            sprintf(
                                '%d-%d-%d %d:%d',
                                $date['year'],
                                $date['month'],
                                $date['day'],
                                $time['hour'],
                                $time['minute']
                            )
                        );
                    } else {
                        $newVal = new \DateTime(
                            sprintf('%d-%d-%d', $testValue['year'], $testValue['month'], $testValue['day'])
                        );
                    }

                    $testValue = $newVal;
                }

                if (!is_array($testValue)) {
                    $testValue = [$testValue];
                }

                $expected = true;

                foreach ($testValue as $val) {
                    switch ($comparator) {
                        case ConditionComparatorEnum::GREATER:
                            $expected = $expected ? $val > $value : false;

                            break;
                        case ConditionComparatorEnum::GREATER_OR_EQUAL:
                            $expected = $expected ? $val >= $value : false;

                            break;
                        case ConditionComparatorEnum::EQUAL:
                            $expected = $expected ? $val == $value : false;

                            break;
                        case ConditionComparatorEnum::LESSER_OR_EQUAL:
                            $expected = $expected ? $val <= $value : false;

                            break;
                        case ConditionComparatorEnum::LESSER:
                            $expected = $expected ? $val < $value : false;

                            break;
                        case ConditionComparatorEnum::IN_SET:
                            $expected = $expected ? in_array($val, $value) : false;

                            break;
                        case ConditionComparatorEnum::NOT_IN_SET:
                            $expected = $expected ? !in_array($val, $value) : false;

                            break;
                    }
                }

                self::assertEquals($expected, $actual);
            }
        }
    }

    public function compareProvider()
    {
        return [
            [
                CustomerOptionTypeEnum::NUMBER,
                1000,
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::NUMBER),
                [2000, 12345, 38, 0, -456],
            ],
            [
                CustomerOptionTypeEnum::TEXT,
                5,
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::TEXT),
                ['abcdef', 'ueothoeu', 'abc', ''],
            ],
            [
                CustomerOptionTypeEnum::SELECT,
                ['val_1', 'val_2'],
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::SELECT),
                ['val_3', 'val_1'],
            ],
            [
                CustomerOptionTypeEnum::MULTI_SELECT,
                ['val_1', 'val_2', 'val_3', 'val_4'],
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::MULTI_SELECT),
                [
                    ['val_3', 'val_1'],
                    ['val_1', 'val_4'],
                    ['val_1', 'val_4', 'val_3'],
                    ['val_1', 'val_4', 'val_3', 'val_2'],
                    ['val_1', 'val_4', 'val_5'],
                    ['val_1', 'val_6'],
                    ['val_1', 'val_4', 'val_3', 'val_2', 'val_7'],
                    ['val_5', 'val_6'],
                    ['val_7'],
                ],
            ],
            [
                CustomerOptionTypeEnum::BOOLEAN,
                true,
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::BOOLEAN),
                [true, false],
            ],
            [
                CustomerOptionTypeEnum::DATE,
                new \DateTime('2018-06-18'),
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::DATE),
                [
                    [
                        'year'  => 1900,
                        'month' => 5,
                        'day'   => 7,
                    ],
                    [
                        'year'  => 2018,
                        'month' => 6,
                        'day'   => 18,
                    ],
                    [
                        'year'  => 2048,
                        'month' => 8,
                        'day'   => 16,
                    ],
                ],
            ],
            [
                CustomerOptionTypeEnum::DATETIME,
                new \DateTime('2018-06-18 12:42'),
                ConditionComparatorEnum::getValuesForCustomerOptionType(CustomerOptionTypeEnum::DATETIME),
                [
                    [
                        'date' => [
                            'year'  => 1900,
                            'month' => 5,
                            'day'   => 7,
                        ],
                        'time' => [
                            'hour'   => 5,
                            'minute' => 8,
                        ],
                    ],
                    [
                        'date' => [
                            'year'  => 2018,
                            'month' => 6,
                            'day'   => 18,
                        ],
                        'time' => [
                            'hour'   => 12,
                            'minute' => 42,
                        ],
                    ],
                    [
                        'date' => [
                            'year'  => 2048,
                            'month' => 8,
                            'day'   => 16,
                        ],
                        'time' => [
                            'hour'   => 8,
                            'minute' => 10,
                        ],
                    ],
                ],
            ],
        ];
    }
}
