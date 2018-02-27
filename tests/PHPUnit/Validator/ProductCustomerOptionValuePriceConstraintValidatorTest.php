<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\Validator;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
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

    private function createCustomerOptionValue(array $configuration): CustomerOptionValueInterface
    {

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
                'invalid entry'    => [
                    new ArrayCollection([12]),
                    'Collection does not contain CustomerOptionValuePrices.'
                ]
            ];
    }

    public function testEmptyCollection()
    {
        $constraint = self::createMock(Constraint::class);
        $this->productCustomerOptionPriceValidator->validate(new ArrayCollection(), $constraint);

        self::assertEquals(0, count($this->violations));
    }

    //todo: add more tests
}
