<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\ProductCustomerOptionValuePriceConstraintValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductCustomerOptionValuePriceConstraintValidatorTest extends TestCase
{
    /** @var ProductCustomerOptionValuePriceConstraintValidator */
    private $productCustomerOptionPriceValidator;

    /** @var array */
    private $violations = [];

    /** @var ChannelInterface[] */
    private $channel;

    /** @var CustomerOptionValueInterface[] */
    private $customerOptionValues;

    //<editor-fold desc="Setup">
    protected function setUp()
    {
        $context = self::createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(
            function (?string $message): void {
                $this->violations[] = $message;
            }
        );

        $this->productCustomerOptionPriceValidator = new ProductCustomerOptionValuePriceConstraintValidator();
        $this->productCustomerOptionPriceValidator->initialize($context);
    }

    private function createPrice(
        string $channelCode,
        string $customerOptionValueCode
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

        $price->method('getCustomerOptionValue')->willReturn($customerOptionValue);

        return $price;
    }

    //</editor-fold>

    /** @dataProvider dataInvalidData */
    public function testInvalidData($value, string $message): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage($message);

        $constraint = self::createMock(Constraint::class);
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
        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection(), $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testWithPricesInDifferentChannels(): void
    {
        $prices
            = [
            $this->createPrice('de_DE', 'value1'),
            $this->createPrice('en_DE', 'value1'),
            $this->createPrice('de_DE', 'value2'),
            $this->createPrice('en_DE', 'value2'),
        ];

        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(0, $this->violations);
    }

    public function testWithPricesInSameChannel(): void
    {
        $prices
            = [
            $this->createPrice('de_DE', 'value1'),
            $this->createPrice('en_DE', 'value1'),
            $this->createPrice('en_DE', 'value1'),
        ];

        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertCount(1, $this->violations);
    }
}
