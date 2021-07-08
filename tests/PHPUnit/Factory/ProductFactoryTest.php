<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Product;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\ProductFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ProductFactoryTest extends TestCase
{
    /** @var ProductFactory */
    private $factory;

    /** @var MockObject */
    private $customerOptionValueRepositoryMock;

    /** @var MockObject */
    private $customerOptionGroupRepositoryMock;

    /** @var CustomerOption */
    private $customerOption;

    /** @var CustomerOptionGroup */
    private $customerOptionGroup;

    /** @var CustomerOptionValuePriceFactoryInterface|MockObject */
    private $customerOptionValuePriceFactory;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $productFactoryMock = $this->createMock(ProductExampleFactory::class);
        $productFactoryMock->expects($this->any())->method('create')->willReturn(new Product());

        $this->customerOptionValueRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->customerOptionGroupRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->customerOptionValuePriceFactory   = $this->createMock(CustomerOptionValuePriceFactoryInterface::class);

        $this->factory = new ProductFactory(
            $productFactoryMock,
            $this->customerOptionGroupRepositoryMock,
            $this->customerOptionValueRepositoryMock,
            $this->customerOptionValuePriceFactory
        );
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithoutCustomerOptionGroup()
    {
        $options = [
            'code' => 'some_product',
            'name' => 'Some Product',
        ];

        $product = $this->factory->create($options);

        $this->assertInstanceOf(ProductInterface::class, $product);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithCustomerOptionGroup()
    {
        $this->setUpCustomerOptionGroupRepository();

        $this->customerOptionGroupRepositoryMock
            ->expects($this->any())
            ->method('findOneBy')->with(['code' => $this->customerOptionGroup->getCode()])
            ->willReturn($this->customerOptionGroup)
        ;

        $options = [
            'code'                  => 'some_product',
            'name'                  => 'Some Product',
            'customer_option_group' => 'some_group',
        ];

        $product = $this->factory->create($options);

        $this->assertInstanceOf(ProductInterface::class, $product);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithValuePrices()
    {
        $this->setUpCustomerOptionGroupRepository();

        $this->customerOptionGroupRepositoryMock
            ->expects($this->any())
            ->method('findOneBy')->with(['code' => $this->customerOptionGroup->getCode()])
            ->willReturn($this->customerOptionGroup)
        ;

        $customerOptionValues = [];
        for ($i = 0; $i < 2; ++$i) {
            $customerOptionValue = new CustomerOptionValue();
            $customerOptionValue->setCode('val_'.$i);
            $customerOptionValue->setCustomerOption($this->customerOption);
            $customerOptionValues[] = $customerOptionValue;

            $this->customerOptionValueRepositoryMock
                ->expects($this->at($i))
                ->method('findOneBy')->with(['code' => $customerOptionValue->getCode()])
                ->willReturn($customerOptionValue)
            ;
        }

        $this->customerOptionValuePriceFactory
            ->expects($this->exactly(2))
            ->method('createFromConfig')
            ->willReturn(new CustomerOptionValuePrice())
        ;

        $this->customerOption->setValues($customerOptionValues);

        $options = [
            'code'                         => 'some_product',
            'name'                         => 'Some Product',
            'customer_option_group'        => 'some_group',
            'customer_option_value_prices' => [
                [
                    'value_code' => 'val_0',
                    'type'       => 'fixed',
                    'amount'     => 1234,
                    'percent'    => 0.12,
                    'channel'    => 'US_WEB',
                ],
                [
                    'value_code' => 'val_1',
                    'type'       => 'percent',
                    'amount'     => 12,
                    'percent'    => 0.123,
                    'channel'    => 'US_WEB',
                ],
                [
                    'value_code' => 'non_existent',
                    'type'       => 'percent',
                    'amount'     => 12,
                    'percent'    => 0.123,
                    'channel'    => 'US_WEB',
                ],
            ],
        ];
        $product = $this->factory->create($options);
        $this->assertInstanceOf(ProductInterface::class, $product);

        $this->assertInstanceOf(CustomerOptionGroupInterface::class, $product->getCustomerOptionGroup());

        $this->assertCount(2, $product->getCustomerOptionValuePrices());
        $this->assertContainsOnly(CustomerOptionValuePriceInterface::class, $product->getCustomerOptionValuePrices());
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function testCreateWithNonExistentGroup()
    {
        $options = [
            'code'                  => 'some_product',
            'name'                  => 'Some Product',
            'customer_option_group' => 'another_group',
        ];

        $this->expectException(\Exception::class);
        $this->factory->create($options);
    }

    /**
     * @throws \Exception
     */
    private function setUpCustomerOptionGroupRepository()
    {
        $customerOption       = new CustomerOption();
        $this->customerOption = $customerOption;
        $customerOption->setCode('some_option');
        $customerOption->setType(CustomerOptionTypeEnum::SELECT);

        $this->customerOptionGroup = new CustomerOptionGroup();
        $this->customerOptionGroup->setCode('some_group');

        $optionAssoc = new CustomerOptionAssociation();
        $optionAssoc->setOption($customerOption);
        $optionAssoc->setGroup($this->customerOptionGroup);

        $this->customerOptionGroup->addOptionAssociation($optionAssoc);
        $customerOption->addGroupAssociation($optionAssoc);
    }
}
