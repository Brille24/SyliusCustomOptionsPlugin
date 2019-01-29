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

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory as BaseFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFactory implements ExampleFactoryInterface
{
    /** @var RepositoryInterface */
    private $customerOptionGroupRepository;

    /** @var RepositoryInterface */
    private $customerOptionValueRepository;

    /** @var RepositoryInterface */
    private $channelRepository;

    /** @var BaseFactory */
    private $baseFactory;

    public function __construct(
        BaseFactory $baseFactory,
        RepositoryInterface $channelRepository,
        RepositoryInterface $customerOptionGroupRepository,
        RepositoryInterface $customerOptionValueRepository
    ) {
        $this->baseFactory                   = $baseFactory;
        $this->customerOptionGroupRepository = $customerOptionGroupRepository;
        $this->customerOptionValueRepository = $customerOptionValueRepository;
        $this->channelRepository             = $channelRepository;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
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
            ->setAllowedTypes('customer_option_value_prices', 'array');
    }

    /**
     * @param array $options
     *
     * @return ProductInterface
     *
     * @throws \Exception
     */
    public function create(array $options = []): ProductInterface
    {
        $options = array_merge(
            [
                'customer_option_group'        => null,
                'customer_option_value_prices' => [],
            ],
            $options
        );

        $customerOptionGroupConfig       = $options['customer_option_group'];
        $customerOptionValuePricesConfig = $options['customer_option_value_prices'];

        unset($options['customer_option_group'], $options['customer_option_value_prices']);

        /** @var ProductInterface $product */
        $product = $this->baseFactory->create($options);

        if ($customerOptionGroupConfig !== null) {
            /** @var CustomerOptionGroupInterface|null $customerOptionGroup */
            $customerOptionGroup = $this->customerOptionGroupRepository->findOneBy(
                ['code' => $customerOptionGroupConfig]
            );

            if ($customerOptionGroup === null) {
                throw new \Exception(
                    sprintf("CustomerOptionGroup with code '%s' does not exist!", $customerOptionGroupConfig)
                );
            }

            $product->setCustomerOptionGroup(
                $customerOptionGroup
            );

            $prices = new ArrayCollection();

            foreach ($customerOptionValuePricesConfig as $valuePriceConfig) {
                $valuePrice = new CustomerOptionValuePrice();

                /** @var CustomerOptionValueInterface|null $value */
                $value = $this->customerOptionValueRepository->findOneBy(['code' => $valuePriceConfig['value_code']]);

                if ($value === null
                    || $product->getCustomerOptionGroup() === null
                    || !in_array($value->getCustomerOption(), $product->getCustomerOptionGroup()->getOptions(), true)
                ) {
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

                /** @var ChannelInterface|null $channel */
                $channel = $this->channelRepository->findOneBy(['code' => $valuePriceConfig['channel']]);

                if ($channel === null) {
                    $channels = new ArrayCollection($this->channelRepository->findAll());
                    $channel  = $channels->first();
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
