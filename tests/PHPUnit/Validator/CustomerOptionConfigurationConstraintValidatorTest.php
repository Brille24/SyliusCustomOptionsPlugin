<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionConfigurationConstraint;
use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionConfigurationConstraintValidator;
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

    public function setUp(): void
    {
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(function (?string $message): void {
            $this->violations[] = $message;
        });

        $this->customerOptionConfigurationValidator = new CustomerOptionConfigurationConstraintValidator();
        $this->customerOptionConfigurationValidator->initialize($context);
    }

    public function testValidateWithInvalidDataStructures(): void
    {
        $constraint = $this->createMock(Constraint::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an array. Got: string');

        $this->customerOptionConfigurationValidator->validate('something', $constraint);
    }

    public function testWithInvalidKeys(): void
    {
        $constraint = $this->createMock(CustomerOptionConfigurationConstraint::class);

        $input = ['something' => ['value' => 'cool'], 'something else' => ['value' => 23]];

        $this->customerOptionConfigurationValidator->validate($input, $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testWithValidKeysAndValidOrder(): void
    {
        $constraint = $this->createMock(CustomerOptionConfigurationConstraint::class);

        $input = ['min' => ['value' => 0], 'max' => ['value' => 23]];

        $this->customerOptionConfigurationValidator->validate($input, $constraint);

        self::assertCount(0, $this->violations);
    }

    /** @dataProvider dataWithValidKeysAndInvalidOrder
     * @param DateTime|int $min
     * @param DateTime|int $max
     */
    public function testWithValidKeysAndInvalidOrder($min, $max): void
    {
        $constraint = $this->createMock(CustomerOptionConfigurationConstraint::class);

        $input = ['max' => ['value' => $min], 'min' => ['value' => $max]];

        $this->customerOptionConfigurationValidator->validate($input, $constraint);

        self::assertCount(1, $this->violations);
    }

    public function dataWithValidKeysAndInvalidOrder(): array
    {
        $dateTime = new DateTime('now');
        $dateTimeLater = clone $dateTime;
        $dateTimeLater->add(new DateInterval('P10D')); // 10 days later

        return
            [
                'int' => [0, 23],
                'datetime' => [$dateTime, $dateTimeLater],
            ];
    }
}
