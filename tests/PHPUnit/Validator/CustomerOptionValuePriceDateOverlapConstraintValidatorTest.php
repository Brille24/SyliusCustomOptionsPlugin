<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRangeInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionValuePriceDateOverlapConstraint;
use Brille24\SyliusCustomerOptionsPlugin\Validator\CustomerOptionValuePriceDateOverlapConstraintValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CustomerOptionValuePriceDateOverlapConstraintValidatorTest extends TestCase
{
    private CustomerOptionValuePriceDateOverlapConstraintValidator $productCustomerOptionPriceValidator;

    private array $violations = [];

    private string $buildingViolation;

    /** @var ChannelInterface[] */
    private array $channel;

    /** @var CustomerOptionValueInterface[] */
    private array $customerOptionValues;

    /** @var CustomerOptionInterface[] */
    private array $customerOptions;

    //<editor-fold desc="Setup">
    protected function setUp(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $context = self::createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(
            function (?string $message): void {
                $this->violations[] = $message;
            },
        );

        $context->method('buildViolation')->willReturnCallback(
            function (string $message) use ($violationBuilder): ConstraintViolationBuilderInterface {
                $this->buildingViolation = $message;

                return $violationBuilder;
            },
        );
        $violationBuilder->method('atPath')->willReturnSelf();
        $violationBuilder->method('setCause')->willReturnSelf();
        $violationBuilder->method('setInvalidValue')->willReturnSelf();
        $violationBuilder->method('addViolation')->willReturnCallback(
            function (): void {
                $this->violations[] = $this->buildingViolation;
            },
        );

        $this->productCustomerOptionPriceValidator = new CustomerOptionValuePriceDateOverlapConstraintValidator();
        $this->productCustomerOptionPriceValidator->initialize($context);
    }

    private function createPrice(
        string $channelCode,
        string $customerOptionCode,
        string $customerOptionValueCode,
        ?DateRangeInterface $dateRange,
    ): CustomerOptionValuePriceInterface {
        $price = self::createMock(CustomerOptionValuePriceInterface::class);

        if (isset($this->channel[$channelCode])) {
            $channel = $this->channel[$channelCode];
        } else {
            $channel = self::createMock(ChannelInterface::class);
            $channel->method('getCode')->willReturn($channelCode);

            $this->channel[$channelCode] = $channel;
        }
        $price->method('getChannel')->willReturn($channel);

        if (isset($this->customerOptionValues[$customerOptionValueCode])) {
            $customerOptionValue = $this->customerOptionValues[$customerOptionValueCode];
        } else {
            $customerOptionValue = self::createMock(CustomerOptionValueInterface::class);
            $customerOptionValue->method('getCode')->willReturn($customerOptionValueCode);

            $this->customerOptionValues[$customerOptionValueCode] = $customerOptionValue;
        }

        if (isset($this->customerOptions[$customerOptionCode])) {
            $customerOption = $this->customerOptions[$customerOptionCode];
        } else {
            $customerOption = self::createMock(CustomerOptionInterface::class);
            $customerOption->method('getCode')->willReturn($customerOptionCode);

            $this->customerOptions[$customerOptionCode] = $customerOption;
        }

        $customerOptionValue->method('getCustomerOption')->willReturn($customerOption);

        $price->method('getCustomerOptionValue')->willReturn($customerOptionValue);
        $price->method('getDateValid')->willReturn($dateRange);

        return $price;
    }

    //</editor-fold>

    /** @dataProvider dataInvalidData */
    public function testInvalidData($value, string $message): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage($message);

        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate($value, $constraint);
    }

    public function dataInvalidData(): array
    {
        return [
            'non object entry' => [
                new stdClass(),
                sprintf('$valuePrices is not type of %s', Collection::class),
            ],
            'invalid entry' => [
                new ArrayCollection([new stdClass()]),
                sprintf('$valuePrices has object not implementing %s', CustomerOptionValuePriceInterface::class),
            ],
        ];
    }

    public function testEmptyCollection(): void
    {
        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection(), $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testWithPricesInDifferentChannels(): void
    {
        $prices
            = [
            $this->createPrice('de_DE', 'some_option', 'value1', null),
            $this->createPrice('en_DE', 'some_option', 'value1', null),
            $this->createPrice('de_DE', 'some_option', 'value2', null),
            $this->createPrice('en_DE', 'some_option', 'value2', null),
        ];

        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testWithPricesInSameChannel(): void
    {
        $prices
            = [
            $this->createPrice('en_DE', 'some_option', 'value1', null),
            $this->createPrice('en_DE', 'some_option', 'value1', null),
        ];

        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(1, $this->violations);
    }

    public function testWithPricesInDifferentChannelWithOverlappingValidDates(): void
    {
        $prices
            = [
            $this->createPrice(
                'de_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-01'), new \DateTime('2020-01-31')),
            ),
            $this->createPrice(
                'en_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-05'), new \DateTime('2020-02-15')),
            ),
        ];

        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testWithPricesInSameChannelWithOverlappingValidDates(): void
    {
        $prices
            = [
            $this->createPrice(
                'en_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-01'), new \DateTime('2020-01-31')),
            ),
            $this->createPrice(
                'en_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-05'), new \DateTime('2020-02-15')),
            ),
        ];

        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(1, $this->violations);
    }

    public function testWithPricesForDifferentValuesWithOverlappingValidDates(): void
    {
        $prices
            = [
            $this->createPrice(
                'de_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-01'), new \DateTime('2020-01-31')),
            ),
            $this->createPrice(
                'de_DE',
                'some_option',
                'value2',
                new DateRange(new \DateTime('2020-01-05'), new \DateTime('2020-02-15')),
            ),
        ];

        $constraint = self::createMock(CustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(0, $this->violations);
    }
}
