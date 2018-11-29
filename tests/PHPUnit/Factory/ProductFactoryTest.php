<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Product;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Factory\ProductFactory;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ProductFactoryTest extends TestCase
{
    /** @var ProductFactory */
    private $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $customerOptionValueRepositoryMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $customerOptionGroupRepositoryMock;

    /** @var CustomerOption */
    private $customerOption;

    /** @var CustomerOptionGroup */
    private $customerOptionGroup;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $channelRepositoryMock = $this->createMock(RepositoryInterface::class);
        $channelRepositoryMock->expects($this->any())->method('findAll')->willReturn([new Channel()]);

        $productFactoryMock = $this->createMock(ProductExampleFactory::class);
        $productFactoryMock->expects($this->any())->method('create')->willReturn(new Product());

        $this->customerOptionValueRepositoryMock = $this->createMock(RepositoryInterface::class);
        $this->customerOptionGroupRepositoryMock = $this->createMock(RepositoryInterface::class);

        $this->factory = new ProductFactory(
            $productFactoryMock,
            $channelRepositoryMock,
            $this->customerOptionGroupRepositoryMock,
            $this->customerOptionValueRepositoryMock
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

        $product = $this->factory->create($options);
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
