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
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ConditionalConstraintValidatorTest extends TestCase
{
    /** @var ConditionalConstraintValidator */
    private $conditionalConstraintValidator;

    /** @var array */
    private $violations;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $requestStack;

    public function setUp()
    {
        $this->violations = [];

        $this->requestStack = self::createMock(RequestStack::class);

        $this->conditionalConstraintValidator = new ConditionalConstraintValidator($this->requestStack);

        $context = self::createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(function (?string $message): void {
            $this->violations[] = $message;
        });

        $this->conditionalConstraintValidator->initialize($context);
    }

    /**
     * @dataProvider requestParamsProvider
     *
     * @param mixed $params
     */
    public function testValidate($params)
    {
        $request          = self::createMock(Request::class);
        $request->request = self::createMock(ParameterBag::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        $request->request->method('get')->with('sylius_add_to_cart')
            ->willReturn($params);

        $customerOptions = $this->createMockCustomerOptions();
        $product         = self::createMock(ProductInterface::class);

        $product->method('getCustomerOptions')->willReturn($customerOptions);

        $orderItem = self::createMock(OrderItemInterface::class);
        $orderItem->method('getProduct')->willReturn($product);

        $condition = new Condition();
        $condition->setComparator(ConditionComparatorEnum::GREATER);
        $condition->setValue(5);
        $condition->setCustomerOption($customerOptions[0]);
        $conditions = [$condition];

        $constraints = [];

        $constraint = new Constraint();
        $constraint->setComparator('equal');
        $constraint->setValue(1);
        $constraint->setCustomerOption($customerOptions[1]);
        $constraints[] = $constraint;

        $constraint = new Constraint();
        $constraint->setComparator('in_set');
        $constraint->setValue(['val_1', 'val_3']);
        $constraint->setCustomerOption($customerOptions[2]);
        $constraints[] = $constraint;

        $conditionalConstraint = new ConditionalConstraint([
            'conditions'  => $conditions,
            'constraints' => $constraints,
        ]);

        //Execute
        $this->conditionalConstraintValidator->validate($orderItem, $conditionalConstraint);

        self::assertNotEmpty($this->violations);
    }

    public function requestParamsProvider()
    {
        return [
            [
                [
                    'customer_options' => [
                        'option_1' => 'some text',
                        'option_2' => '1',
                        'option_3' => 'val_1',
                    ],
                ],
            ],
            [
                [
                    'customer_options' => [
                        'option_1' => 'abc',
                        'option_3' => 'val_2',
                    ],
                ],
            ],
        ];
    }

    private function createMockCustomerOptions()
    {
        $customerOptions = [];

        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('option_1');
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::TEXT);
        $customerOptions[] = $customerOption;

        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('option_2');
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::NUMBER);
        $customerOptions[] = $customerOption;

        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn('option_3');
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::SELECT);
        $customerOptions[] = $customerOption;

        return $customerOptions;
    }
}
