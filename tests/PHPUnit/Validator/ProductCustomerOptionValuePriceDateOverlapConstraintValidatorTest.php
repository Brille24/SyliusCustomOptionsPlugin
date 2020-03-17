<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRangeInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\ProductCustomerOptionValuePriceDateOverlapConstraint;
use Brille24\SyliusCustomerOptionsPlugin\Validator\ProductCustomerOptionValuePriceDateOverlapConstraintValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductCustomerOptionValuePriceDateOverlapConstraintValidatorTest extends TestCase
{
    /** @var ProductCustomerOptionValuePriceDateOverlapConstraintValidator */
    private $productCustomerOptionPriceValidator;

    /** @var array */
    private $violations = [];

    /** @var ChannelInterface[] */
    private $channel;

    /** @var CustomerOptionValueInterface[] */
    private $customerOptionValues;

    /** @var CustomerOptionInterface[] */
    private $customerOptions;

    //<editor-fold desc="Setup">
    protected function setUp()
    {
        $context = self::createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(
            function (?string $message): void {
                $this->violations[] = $message;
            }
        );

        $this->productCustomerOptionPriceValidator = new ProductCustomerOptionValuePriceDateOverlapConstraintValidator();
        $this->productCustomerOptionPriceValidator->initialize($context);
    }

    private function createPrice(
        string $channelCode,
        string $customerOptionCode,
        string $customerOptionValueCode,
        ?DateRangeInterface $dateRange
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

        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate($value, $constraint);
    }

    public function dataInvalidData(): array
    {
        return [
            'non object entry' => [
                new stdClass(),
                'Expected an instance of Doctrine\Common\Collections\Collection. Got: stdClass',
            ],
            'invalid entry' => [
                new ArrayCollection([new stdClass()]),
                'Expected an implementation of "Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface". Got: stdClass',
            ],
        ];
    }

    public function testEmptyCollection(): void
    {
        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
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

        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
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

        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
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
                new DateRange(new \DateTime('2020-01-01'), new \DateTime('2020-01-31'))
            ),
            $this->createPrice(
                'en_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-05'), new \DateTime('2020-02-15'))
            ),
        ];

        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
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
                new DateRange(new \DateTime('2020-01-01'), new \DateTime('2020-01-31'))
            ),
            $this->createPrice(
                'en_DE',
                'some_option',
                'value1',
                new DateRange(new \DateTime('2020-01-05'), new \DateTime('2020-02-15'))
            ),
        ];

        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
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
                new DateRange(new \DateTime('2020-01-01'), new \DateTime('2020-01-31'))
            ),
            $this->createPrice(
                'de_DE',
                'some_option',
                'value2',
                new DateRange(new \DateTime('2020-01-05'), new \DateTime('2020-02-15'))
            ),
        ];

        $constraint = self::createMock(ProductCustomerOptionValuePriceDateOverlapConstraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(0, $this->violations);
    }
}
