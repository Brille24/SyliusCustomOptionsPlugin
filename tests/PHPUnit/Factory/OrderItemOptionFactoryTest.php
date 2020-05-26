<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactory;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class OrderItemOptionFactoryTest extends TestCase
{
    /** @var array */
    private $customerOptions = [];

    /** @var OrderItemOptionFactoryInterface */
    private $orderItemOptionFactory;

    /** @var ChannelInterface */
    private $channel;

    public function setUp(): void
    {
        $baseFactory = self::createMock(FactoryInterface::class);
        $baseFactory->method('createNew')->willReturn(new OrderItemOption());

        $this->channel      = self::createMock(ChannelInterface::class);
        $customerOptionRepo = self::createMock(CustomerOptionRepositoryInterface::class);
        $customerOptionRepo->method('findOneByCode')->willReturnCallback(function (string $code) {
            if (array_key_exists($code, $this->customerOptions)) {
                return $this->customerOptions[$code];
            }

            return null;
        });

        $valueResolver = self::createMock(CustomerOptionValueResolverInterface::class);
        $valueResolver->method('resolve')->willReturnCallback(
            function (CustomerOptionInterface $customerOption, $valueToMatch) {
                foreach ($customerOption->getValues() as $value) {
                    if ($value->getCode() === $valueToMatch) {
                        return $value;
                    }
                }

                return null;
            }
        );

        $baseFactory->method('createNew')->willReturn(self::createMock(OrderItemOptionInterface::class));

        $this->orderItemOptionFactory = new OrderItemOptionFactory($baseFactory, $this->channel, $customerOptionRepo, $valueResolver);
    }

    private function addCustomerOption(CustomerOptionInterface $customerOption)
    {
        $this->customerOptions[$customerOption->getCode()] = $customerOption;
    }

    private function createCustomerOption(string $code): CustomerOptionInterface
    {
        $customerOption = self::createMock(CustomerOptionInterface::class);
        $customerOption->method('getCode')->willReturn($code);
        $customerOption->method('getType')->willReturn(CustomerOptionTypeEnum::SELECT);

        return $customerOption;
    }

    public function testCreateForOptionAndValue(): void
    {
        $customerOption = self::createMock(CustomerOptionInterface::class);
        $value          = 'something';

        $orderItemOption = $this->orderItemOptionFactory->createForOptionAndValue($customerOption, $value);
        $this->assertInstanceOf(OrderItemOptionInterface::class, $orderItemOption);
    }

    public function testCreateNewFromStringsWithInvalidCustomerOptionCode()
    {
        self::expectException(\Exception::class);
        self::expectExceptionMessage('Could not find customer option with code');

        $this->orderItemOptionFactory->createNewFromStrings('something', 'value');
    }

    public function testCreateNewFromStringWithInvalidValue()
    {
        $customerOption = $this->createCustomerOption('something');
        $customerOption->method('getValues')->willReturn(new ArrayCollection());

        $this->addCustomerOption($customerOption);

        $customerOption = $this->orderItemOptionFactory->createNewFromStrings('something', 'value');

        self::assertNull($customerOption->getCustomerOptionValue());
        self::assertNull($customerOption->getCustomerOptionValueCode());
    }

    public function testCreateNewFromStringWithValidValue()
    {
        $customerOptionValuePrice = self::createMock(CustomerOptionValuePriceInterface::class);

        $customerOptionValue = self::createMock(CustomerOptionValueInterface::class);
        $customerOptionValue->method('getCode')->willReturn('value');
        $customerOptionValue->method('getName')->willReturn('some value');
        $customerOptionValue->method('getPriceForChannel')->with($this->channel)->willReturn($customerOptionValuePrice);

        $customerOption = $this->createCustomerOption('something');
        $customerOption->method('getValues')->willReturn(new ArrayCollection([$customerOptionValue]));

        $this->addCustomerOption($customerOption);

        $orderItemOption = $this->orderItemOptionFactory->createNewFromStrings('something', 'value');

        self::assertEquals($customerOptionValue, $orderItemOption->getCustomerOptionValue());
        self::assertEquals($customerOptionValue->getCode(), $orderItemOption->getCustomerOptionValueCode());
    }
}
