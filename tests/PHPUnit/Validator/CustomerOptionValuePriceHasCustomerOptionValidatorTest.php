<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionValuePriceHasCustomerOptionConstraint;
use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionValuePriceHasCustomerOptionValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CustomerOptionValuePriceHasCustomerOptionValidatorTest extends TestCase
{
    private CustomerOptionValuePriceHasCustomerOptionValidator $validator;

    private int $violationCount = 0;

    protected function setUp(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('atPath')->willReturnSelf();
        $violationBuilder->method('setCause')->willReturnSelf();
        $violationBuilder->method('addViolation')->willReturnCallback(function (): void {
            ++$this->violationCount;
        });

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('buildViolation')->willReturn($violationBuilder);

        $this->validator = new CustomerOptionValuePriceHasCustomerOptionValidator();
        $this->validator->initialize($context);
    }

    public function testAcceptsPricesOfTheProductsCustomerOptions(): void
    {
        $customerOption = $this->createCustomerOption('color');

        $this->validator->validate(
            $this->createProduct([$customerOption], [$this->createPrice($customerOption)]),
            new CustomerOptionValuePriceHasCustomerOptionConstraint(),
        );

        self::assertSame(0, $this->violationCount);
    }

    public function testRejectsPricesOfOtherCustomerOptions(): void
    {
        $this->validator->validate(
            $this->createProduct(
                [$this->createCustomerOption('color')],
                [$this->createPrice($this->createCustomerOption('material'))],
            ),
            new CustomerOptionValuePriceHasCustomerOptionConstraint(),
        );

        self::assertSame(1, $this->violationCount);
    }

    public function testIgnoresPricesWithoutCustomerOptionValue(): void
    {
        $this->validator->validate(
            $this->createProduct([$this->createCustomerOption('color')], [$this->createPrice(null)]),
            new CustomerOptionValuePriceHasCustomerOptionConstraint(),
        );

        self::assertSame(0, $this->violationCount);
    }

    private function createCustomerOption(string $code): CustomerOptionInterface
    {
        $customerOption = $this->createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn($code);

        return $customerOption;
    }

    private function createPrice(?CustomerOptionInterface $customerOption): CustomerOptionValuePriceInterface
    {
        $customerOptionValue = null;
        if ($customerOption !== null) {
            $customerOptionValue = $this->createMock(CustomerOptionValueInterface::class);
            $customerOptionValue->method('getCode')->willReturn($customerOption->getCode() . '_value');
            $customerOptionValue->method('getCustomerOption')->willReturn($customerOption);
        }

        $price = $this->createMock(CustomerOptionValuePriceInterface::class);
        $price->method('getCustomerOptionValue')->willReturn($customerOptionValue);

        return $price;
    }

    /**
     * @param CustomerOptionInterface[] $customerOptions
     * @param CustomerOptionValuePriceInterface[] $prices
     */
    private function createProduct(array $customerOptions, array $prices): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCustomerOptions')->willReturn($customerOptions);
        $product->method('getCustomerOptionValuePrices')->willReturn(new ArrayCollection($prices));

        return $product;
    }
}
