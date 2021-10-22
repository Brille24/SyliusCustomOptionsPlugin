<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionAssociationConstraintValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CustomerOptionAssociationConstraintValidatorTest extends TestCase
{
    /** @var CustomerOptionAssociationConstraintValidator */
    private $customerOptionAssociationConstraintValidator;

    /** @var array */
    private $violations = [];

    /** @var CustomerOptionInterface[] */
    private $customerOptions = [];

    //<editor-fold desc="Setup" default="collapsed">
    protected function setUp(): void
    {
        $this->customerOptionAssociationConstraintValidator = new CustomerOptionAssociationConstraintValidator();

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(function (?string $message): void {
            $this->violations[] = $message;
        });
        $this->customerOptionAssociationConstraintValidator->initialize($context);
    }

    private function createCustomerOptionAssociation(string $customerOptionCode): CustomerOptionAssociationInterface
    {
        if (isset($this->customerOptions[$customerOptionCode])) {
            $customerOption = $this->customerOptions[$customerOptionCode];
        } else {
            $customerOption = $this->createMock(CustomerOptionInterface::class);
            $customerOption->method('getCode')->willReturn($customerOptionCode);
            $this->customerOptions[$customerOptionCode] = $customerOption;
        }

        $customerOptionAssociation = $this->createMock(CustomerOptionAssociationInterface::class);
        $customerOptionAssociation->method('getOption')->willReturn($customerOption);

        return $customerOptionAssociation;
    }

    //</editor-fold>

    public function testWrongElementType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected an instance of Doctrine\Common\Collections\Collection. Got: integer');

        $constraint = $this->createMock(Constraint::class);
        $this->customerOptionAssociationConstraintValidator->validate(1, $constraint);
    }

    public function testWrongElementTypeInList(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected an instance of Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociationInterface. Got: integer'
        );

        $constraint = $this->createMock(Constraint::class);
        $this->customerOptionAssociationConstraintValidator->validate(new ArrayCollection([1]), $constraint);
    }

    public function testValidate(): void
    {
        $collection = new ArrayCollection([]);
        $constraint = $this->createMock(Constraint::class);

        $this->customerOptionAssociationConstraintValidator->validate($collection, $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testValidateWithDuplicate(): void
    {
        $collection = new ArrayCollection(
            [
                $this->createCustomerOptionAssociation('customerOption1'),
                $this->createCustomerOptionAssociation('customerOption1'),
            ]
        );
        $constraint = $this->createMock(Constraint::class);

        $this->customerOptionAssociationConstraintValidator->validate($collection, $constraint);

        self::assertCount(1, $this->violations);
    }

    public function testValidValidate(): void
    {
        $collection = new ArrayCollection(
            [
                $this->createCustomerOptionAssociation('customerOption1'),
                $this->createCustomerOptionAssociation('customerOption2'),
            ]
        );
        $constraint = $this->createMock(Constraint::class);

        $this->customerOptionAssociationConstraintValidator->validate($collection, $constraint);

        self::assertCount(0, $this->violations);
    }
}
