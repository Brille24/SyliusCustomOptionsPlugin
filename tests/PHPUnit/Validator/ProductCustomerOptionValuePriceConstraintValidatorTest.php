<?php

declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Validator;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Validator\Constraints\ProductCustomerOptionValuePriceConstraintValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductCustomerOptionValuePriceConstraintValidatorTest extends TestCase
{
    /** @var ProductCustomerOptionValuePriceConstraintValidator */
    private $productCustomerOptionPriceValidator;

    /** @var array */
    private $violations;

    /** @var ChannelInterface[] */
    private $channel;

    /** @var CustomerOptionValueInterface[] */
    private $customerOptionValues;

    //<editor-fold desc="Setup">
    protected function setUp()
    {
        $context = self::createMock(ExecutionContextInterface::class);
        $context->method('addViolation')->willReturnCallback(function (?string $message): void {
            $this->violations[] = $message;
        });

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
        return
            [
                'not a collection' => [12, 'Value is not a Collection.'],
                'invalid entry' => [
                    new ArrayCollection([12]),
                    'Collection does not contain CustomerOptionValuePrices.',
                ],
            ];
    }

    public function testEmptyCollection(): void
    {
        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection(), $constraint);

        self::assertEquals(0, count($this->violations));
    }

    public function testWithPricesInDifferentChannels(): void
    {
        $prices =
            [
                $this->createPrice('de_DE', 'value1'),
                $this->createPrice('en_DE', 'value1'),
                $this->createPrice('de_DE', 'value2'),
                $this->createPrice('en_DE', 'value2'),
            ];

        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertEquals(0, count($this->violations));
    }

    public function testWithPricesInSameChannel(): void
    {
        $prices =
            [
                $this->createPrice('de_DE', 'value1'),
                $this->createPrice('en_DE', 'value1'),
                $this->createPrice('en_DE', 'value1'),
            ];

        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection($prices), $constraint);

        self::assertEquals(1, count($this->violations));
    }
}
