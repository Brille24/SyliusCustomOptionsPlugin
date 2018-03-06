<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Factory;


use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionAssociation;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroup;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValue;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Entity\Product;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Factory\ProductFactory;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductOption;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ProductFactoryTest extends TestCase
{
    /** @var ProductFactory */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
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
        $generalRepositoryMock = $this->createMock(RepositoryInterface::class);
        $generalRepositoryMock->expects($this->any())->method('findAll')->willReturn([]);

        $taxonRepositoryMock = $this->createMock(RepositoryInterface::class);
        $taxonRepositoryMock->expects($this->any())->method('findAll')->willReturn([new Taxon()]);

        $productAttributeRepositoryMock = $this->createMock(RepositoryInterface::class);
        $productAttributeRepositoryMock->expects($this->any())->method('findAll')->willReturn([new ProductAttribute()]);

        $productOptionRepositoryMock = $this->createMock(RepositoryInterface::class);
        $productOptionRepositoryMock->expects($this->any())->method('findAll')->willReturn([new ProductOption()]);

        $channelRepositoryMock = $this->createMock(RepositoryInterface::class);
        $channelRepositoryMock->expects($this->any())->method('findAll')->willReturn([new Channel()]);

        $locale = new Locale();
        $locale->setCode('en_US');

        $localeRepositoryMock = $this->createMock(RepositoryInterface::class);
        $localeRepositoryMock->expects($this->any())->method('findAll')->willReturn([$locale]);

        $productFactoryMock = $this->createMock(FactoryInterface::class);
        $productFactoryMock->expects($this->any())->method('createNew')->willReturn(new Product());

        $productTaxonFactoryMock = $this->createMock(FactoryInterface::class);
        $productTaxonFactoryMock->expects($this->any())->method('createNew')->willReturn(new ProductTaxon());

        $customerOption = new CustomerOption();
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

        $this->customerOptionValueRepositoryMock = $this->createMock(RepositoryInterface::class);

        $this->customerOptionGroupRepositoryMock = $this->createMock(RepositoryInterface::class);

        $this->factory = new ProductFactory(
            $productFactoryMock,
            $this->createMock(FactoryInterface::class),
            $this->createMock(FactoryInterface::class),
            $this->createMock(ProductVariantGeneratorInterface::class),
            $this->createMock(FactoryInterface::class),
            $this->createMock(FactoryInterface::class),
            $productTaxonFactoryMock,
            $this->createMock(ImageUploaderInterface::class),
            $this->createMock(SlugGeneratorInterface::class),
            $taxonRepositoryMock,
            $productAttributeRepositoryMock,
            $productOptionRepositoryMock,
            $channelRepositoryMock,
            $localeRepositoryMock,
            $this->customerOptionGroupRepositoryMock,
            $this->customerOptionValueRepositoryMock
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function testCreateWithoutCustomerOptionGroup(){
        $options = [
            'code' => 'some_product',
            'name' => 'Some Product',
        ];

        $product = $this->factory->create($options);

        $this->assertInstanceOf(ProductInterface::class, $product);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function testCreateWithCustomerOptionGroup(){
        $this->customerOptionGroupRepositoryMock
            ->expects($this->any())
            ->method('findOneBy')->with(['code' => $this->customerOptionGroup->getCode()])
            ->willReturn($this->customerOptionGroup)
        ;

        $options = [
            'code' => 'some_product',
            'name' => 'Some Product',
            'customer_option_group' => 'some_group',
        ];

        $product = $this->factory->create($options);

        $this->assertInstanceOf(ProductInterface::class, $product);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function testCreateWithValuePrices(){
        $this->customerOptionGroupRepositoryMock
            ->expects($this->any())
            ->method('findOneBy')->with(['code' => $this->customerOptionGroup->getCode()])
            ->willReturn($this->customerOptionGroup)
        ;

        $customerOptionValues = [];
        for($i = 0; $i < 2; $i++){
            $customerOptionValue = new CustomerOptionValue();
            $customerOptionValue->setCode('val_' .  $i);
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
            'code' => 'some_product',
            'name' => 'Some Product',
            'customer_option_group' => 'some_group',
            'customer_option_value_prices' => [
                [
                    'value_code' => 'val_0',
                    'type' => 'fixed',
                    'amount' => 1234,
                    'percent' => 0.12,
                    'channel' => 'US_WEB',
                ],
                [
                    'value_code' => 'val_1',
                    'type' => 'percent',
                    'amount' => 12,
                    'percent' => 0.123,
                    'channel' => 'US_WEB',
                ],
                [
                    'value_code' => 'non_existent',
                    'type' => 'percent',
                    'amount' => 12,
                    'percent' => 0.123,
                    'channel' => 'US_WEB',
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
     * @throws \Exception
     */
    public function testCreateWithNonExistentGroup(){
        $options = [
            'code' => 'some_product',
            'name' => 'Some Product',
            'customer_option_group' => 'another_group',
        ];

        $this->expectException(\Exception::class);

        $product = $this->factory->create($options);
    }
}