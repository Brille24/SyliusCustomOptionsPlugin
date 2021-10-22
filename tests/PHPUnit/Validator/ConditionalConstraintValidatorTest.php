<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Condition;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator\Constraint;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\ConditionComparatorEnum;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Validator\ConditionalConstraintValidator;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\ConditionalConstraint;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ConditionalConstraintValidatorTest extends TestCase
{
    /** @var ConditionalConstraintValidator */
    private $conditionalConstraintValidator;

    /** @var array<string|null> */
    private $violations;

    /** @var MockObject|Request */
    private $request;

    public function setUp(): void
    {
        $this->violations = [];
        $this->request = new Request();
        $requestStack = $this->createConfiguredMock(RequestStack::class, [
            'getCurrentRequest' => $this->request
        ]);

        $this->conditionalConstraintValidator = new ConditionalConstraintValidator($requestStack);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(function (?string $message): void {
            $this->violations[] = $message;
        });

        $this->conditionalConstraintValidator->initialize($context);
    }

    /**
     * @dataProvider requestParamsProvider
     *
     * @param mixed $customerEnteredValues
     */
    public function testValidate($customerEnteredValues)
    {
        $this->request->request = new InputBag([
            'sylius_add_to_cart' => ['customer_options' => $customerEnteredValues]
        ]);

        $customerOptions = $this->createMockCustomerOptions();
        $product = $this->createMock(ProductInterface::class);

        $product->method('getCustomerOptions')->willReturn($customerOptions);

        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getProduct')->willReturn($product);

        $condition = new Condition();
        $condition->setComparator(ConditionComparatorEnum::GREATER);
        $condition->setValue(5);
        $condition->setCustomerOption($customerOptions[0]);
        $conditions = [$condition];

        $constraints = [];

        $constraint = new Constraint();
        $constraint->setComparator(ConditionComparatorEnum::EQUAL);
        $constraint->setValue(1);
        $constraint->setCustomerOption($customerOptions[1]);
        $constraints[] = $constraint;

        $constraint = new Constraint();
        $constraint->setComparator(ConditionComparatorEnum::IN_SET);
        $constraint->setValue(['val_1', 'val_3']);
        $constraint->setCustomerOption($customerOptions[2]);
        $constraints[] = $constraint;

        $conditionalConstraint = new ConditionalConstraint([
            'conditions' => $conditions,
            'constraints' => $constraints,
        ]);

        //Execute
        $this->conditionalConstraintValidator->validate($orderItem, $conditionalConstraint);

        self::assertNotEmpty($this->violations);
    }

    public function requestParamsProvider(): array
    {
        return [
            'some other test' => [['option_1' => 'some text', 'option_2' => '1', 'option_3' => 'val_1']],
            'missing option values' => [['option_1' => 'abc', 'option_3' => 'val_2']],
        ];
    }

    /**
     * @return array<CustomerOptionInterface>
     */
    private function createMockCustomerOptions(): array
    {
        $customerOptions = [];

        $customerOption = $this->createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('option_1');
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::TEXT);
        $customerOptions[] = $customerOption;

        $customerOption = $this->createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('option_2');
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::NUMBER);
        $customerOptions[] = $customerOption;

        $customerOption = $this->createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('option_3');
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::SELECT);
        $customerOptions[] = $customerOption;

        return $customerOptions;
    }
}
