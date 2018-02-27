<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Validator;

use Brille24\CustomerOptionsPlugin\Validator\Constraints\CustomerOptionConfigurationConstraintValidator;
use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomerOptionConfigurationConstraintValidatorTest extends TestCase
{
    /** @var CustomerOptionConfigurationConstraintValidator */
    private $customerOptionConfigurationValidator;

    /** @var array */
    private $violations = [];

    public function setUp()
    {
        $context = self::createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(function (?string $message): void {
            $this->violations[] = $message;
        });

        $this->customerOptionConfigurationValidator = new CustomerOptionConfigurationConstraintValidator();
        $this->customerOptionConfigurationValidator->initialize($context);
    }

    /** @dataProvider dataValidateWithInvalidDataStructures */
    public function testValidateWithInvalidDataStructures($input): void
    {
        $constraint = self::createMock(Constraint::class);

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Can not validate configuration');

        $this->customerOptionConfigurationValidator->validate($input, $constraint);
    }

    public function dataValidateWithInvalidDataStructures(): array
    {
        return [
            'no array'              => ['something'],
            'array too short'       => [[]],
            'array still too short' => [['something' => 'value']],
        ];
    }

    public function testWithInvalidKeys()
    {
        $constraint = self::createMock(Constraint::class);

        $input = ['something' => ['value' => 'cool'], 'something else' => ['value' => 23]];

        $this->customerOptionConfigurationValidator->validate($input, $constraint);

        self::assertEquals(0, count($this->violations));
    }

    public function testWithValidKeysAndValidOrder()
    {
        $constraint = self::createMock(Constraint::class);

        $input = ['min' => ['value' => 0], 'max' => ['value' => 23]];

        $this->customerOptionConfigurationValidator->validate($input, $constraint);

        self::assertEquals(0, count($this->violations));
    }

    /** @dataProvider dataWithValidKeysAndInvalidOrder */
    public function testWithValidKeysAndInvalidOrder($min, $max)
    {
        $constraint = self::createMock(Constraint::class);

        $input = ['max' => ['value' => $min], 'min' => ['value' => $max]];

        $this->customerOptionConfigurationValidator->validate($input, $constraint);

        self::assertEquals(1, count($this->violations));
    }

    public function dataWithValidKeysAndInvalidOrder(): array
    {
        $dateTime      = new DateTime('now');
        $dateTimeLater = clone($dateTime);
        $dateTimeLater->add(new DateInterval('P10D')); // 10 days later
        return
            [
                'int'      => [0, 23],
                'datetime' => [$dateTime, $dateTimeLater]
            ];
    }

}
