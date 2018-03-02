<?php
declare(strict_types=1);

namespace Tests\Brille24\CustomerOptionsPlugin\PHPUnit\Factory;


use Brille24\CustomerOptionsPlugin\Entity\Product;
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

    public function setUp()
    {
        $generalRepositoryMock = $this->createMock(RepositoryInterface::class);
        $generalRepositoryMock->expects($this->any())->method('findAll')->willReturn([]);

        $taxonRepositoryMock = $this->createMock(RepositoryInterface::class);
//        $taxonRepositoryMock->expects($this->any())->method('findAll')->willReturn([$this->createMock(TaxonInterface::class)]);
        $taxonRepositoryMock->expects($this->any())->method('findAll')->willReturn([new Taxon()]);

        $productAttributeRepositoryMock = $this->createMock(RepositoryInterface::class);
//        $productAttributeRepositoryMock->expects($this->any())->method('findAll')->willReturn([$this->createMock(ProductAttributeInterface::class)]);
        $productAttributeRepositoryMock->expects($this->any())->method('findAll')->willReturn([new ProductAttribute()]);

        $productOptionRepositoryMock = $this->createMock(RepositoryInterface::class);
//        $productOptionRepositoryMock->expects($this->any())->method('findAll')->willReturn([$this->createMock(ProductOptionInterface::class)]);
        $productOptionRepositoryMock->expects($this->any())->method('findAll')->willReturn([new ProductOption()]);

        $channelRepositoryMock = $this->createMock(RepositoryInterface::class);
//        $channelRepositoryMock->expects($this->any())->method('findAll')->willReturn([$this->createMock(ChannelInterface::class)]);
        $channelRepositoryMock->expects($this->any())->method('findAll')->willReturn([new Channel()]);

        $localeRepositoryMock = $this->createMock(RepositoryInterface::class);
//        $localeRepositoryMock->expects($this->any())->method('findAll')->willReturn([$this->createMock(LocaleInterface::class)]);
        $locale = new Locale();
        $locale->setCode('en_US');
        $localeRepositoryMock->expects($this->any())->method('findAll')->willReturn([$locale]);


        $productFactoryMock = $this->createMock(FactoryInterface::class);
        $productFactoryMock->expects($this->any())->method('createNew')->willReturn(new Product());

        $productTaxonFactoryMock = $this->createMock(FactoryInterface::class);
        $productTaxonFactoryMock->expects($this->any())->method('createNew')->willReturn(new ProductTaxon());

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
            $generalRepositoryMock,
            $generalRepositoryMock
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

    public function testCreateWithCustomerOptionGroup(){

    }
}