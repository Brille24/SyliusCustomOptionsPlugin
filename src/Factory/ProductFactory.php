<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory as BaseFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFactory extends BaseFactory
{
    /** @var RepositoryInterface */
    private $customerOptionGroupRepository;

    /** @var RepositoryInterface */
    private $customerOptionValueRepository;

    /** @var RepositoryInterface */
    private $channelRepository;

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
        RepositoryInterface $customerOptionGroupRepository,
        RepositoryInterface $customerOptionValueRepository
    ) {
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->customerOptionValueRepository = $customerOptionValueRepository;
        $this->channelRepository = $channelRepository;

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
            ->setAllowedTypes('customer_option_group', ['string', CustomerOptionGroupInterface::class, 'null'])
            ->setNormalizer(
                'customer_option_group',
                LazyOption::findOneBy($this->customerOptionGroupRepository, 'code')
            )

            ->setDefault('customer_option_value_prices', [])
            ->setAllowedTypes('customer_option_value_prices', 'array')
        ;
    }

    /**
     * @param array $options
     *
     * @return ProductInterface
     *
     * @throws \Exception
     */
    public function create(array $options = []): \Sylius\Component\Core\Model\ProductInterface
    {
        $options = array_merge(['customer_option_value_prices' => []], $options);

        /** @var ProductInterface $product */
        $product = parent::create($options);

        if (isset($options['customer_option_group'])) {
            /** @var CustomerOptionGroupInterface $customerOptionGroup */
            $customerOptionGroup = $this->customerOptionGroupRepository->findOneBy(['code' => $options['customer_option_group']]);

            if ($customerOptionGroup === null) {
                throw new \Exception(sprintf("CustomerOptionGroup with code '%s' does not exist!", $options['customer_option_group']));
            }

            $product->setCustomerOptionGroup(
                $customerOptionGroup
            );

            $prices = new ArrayCollection();

            foreach ($options['customer_option_value_prices'] as $valuePriceConfig) {
                $valuePrice = new CustomerOptionValuePrice();

                /** @var CustomerOptionValueInterface $value */
                $value = $this->customerOptionValueRepository->findOneBy(['code' => $valuePriceConfig['value_code']]);

                if ($value === null ||
                    ($value !== null && !in_array($value->getCustomerOption(), $product->getCustomerOptionGroup()->getOptions())
                    )) {
                    continue;
                }

                $valuePrice->setCustomerOptionValue($value);

                if ($valuePriceConfig['type'] === 'fixed') {
                    $valuePrice->setType(CustomerOptionValuePrice::TYPE_FIXED_AMOUNT);
                } elseif ($valuePriceConfig['type'] === 'percent') {
                    $valuePrice->setType(CustomerOptionValuePrice::TYPE_PERCENT);
                } else {
                    throw new \Exception(sprintf("Value price type '%s' does not exist!", $valuePriceConfig['type']));
                }

                $valuePrice->setAmount($valuePriceConfig['amount']);
                $valuePrice->setPercent($valuePriceConfig['percent']);

                /** @var ChannelInterface $channel */
                $channel = $this->channelRepository->findOneBy(['code' => $valuePriceConfig['channel']]);

                if ($channel === null) {
                    $channels = new ArrayCollection($this->channelRepository->findAll());
                    $channel = $channels->first();
                }

                $valuePrice->setChannel($channel);

                $valuePrice->setProduct($product);

                $prices[] = $valuePrice;
            }
            $product->setCustomerOptionValuePrices($prices);
        }

        return $product;
    }
}
