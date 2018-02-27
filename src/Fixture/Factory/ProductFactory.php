<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Fixture\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory as BaseFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFactory extends BaseFactory
{
    private $customerOptionGroupRepository;

    public function __construct(
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $channelPricing,
        ProductVariantGeneratorInterface $variantGenerator,
        FactoryInterface $productAttributeValueFactory,
        FactoryInterface $productImageFactory,
        FactoryInterface $productTaxonFactory,
        ImageUploaderInterface $imageUploader,
        SlugGeneratorInterface $slugGenerator,
        RepositoryInterface $taxonRepository,
        RepositoryInterface $productAttributeRepository,
        RepositoryInterface $productOptionRepository,
        RepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository,
        RepositoryInterface $customerOptionGroupRepository
    )
    {
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;

        parent::__construct(
            $productFactory,
            $productVariantFactory,
            $channelPricing,
            $variantGenerator,
            $productAttributeValueFactory,
            $productImageFactory,
            $productTaxonFactory,
            $imageUploader,
            $slugGenerator,
            $taxonRepository,
            $productAttributeRepository,
            $productOptionRepository,
            $channelRepository,
            $localeRepository
        );
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault(
                'customer_option_group',
                LazyOption::randomOneOrNull($this->customerOptionGroupRepository, 5)
            )
            ->setAllowedTypes('customer_option_group', ['string', CustomerOptionGroupInterface::class, null])
            ->setNormalizer(
                'customer_option_group',
                LazyOption::findOneBy($this->customerOptionGroupRepository, 'code')
            )

            ->setDefault('customer_option_value_prices', [])
            ->setAllowedTypes('customer_option_value_prices', 'array')
            ->setNormalizer('customer_option_value_prices', function(Options $options, array $valuePrices): array{

            })
        ;
    }

    public function create(array $options = []): ProductInterface
    {
        $product = parent::create($options);

//        return $product;
    }
}
