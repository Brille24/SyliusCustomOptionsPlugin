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
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory as BaseFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFactory implements ExampleFactoryInterface
{
    private RepositoryInterface $customerOptionGroupRepository;
    private RepositoryInterface $customerOptionValueRepository;
    private BaseFactory $baseFactory;
    private OptionsResolver $optionsResolver;
    private CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory;

    public function __construct(
        BaseFactory $baseFactory,
        RepositoryInterface $customerOptionGroupRepository,
        RepositoryInterface $customerOptionValueRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory
    ) {
        $this->baseFactory                     = $baseFactory;
        $this->customerOptionGroupRepository   = $customerOptionGroupRepository;
        $this->customerOptionValueRepository   = $customerOptionValueRepository;
        $this->customerOptionValuePriceFactory = $customerOptionValuePriceFactory;

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
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
                LazyOption::getOneBy($this->customerOptionGroupRepository, 'code')
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
        $currentOptions =
            $this->optionsResolver->resolve([
                'customer_option_group'        => $options['customer_option_group'] ?? null,
                'customer_option_value_prices' => $options['customer_option_value_prices'] ?? [],
            ]);

        unset($options['customer_option_group'], $options['customer_option_value_prices']);

        /** @var ProductInterface $product */
        $product = $this->baseFactory->create($options);

        if ($currentOptions['customer_option_group'] === null) {
            return $product;
        }

        $product->setCustomerOptionGroup($currentOptions['customer_option_group']);
        $prices = new ArrayCollection();

        foreach ($currentOptions['customer_option_value_prices'] as $valuePriceConfig) {
            /** @var CustomerOptionValueInterface|null $value */
            $value = $this->customerOptionValueRepository->findOneBy(['code' => $valuePriceConfig['value_code']]);

            if ($value === null
                || $product->getCustomerOptionGroup() === null
                || !in_array($value->getCustomerOption(), $product->getCustomerOptionGroup()->getOptions(), true)
            ) {
                continue;
            }

            $valuePrice = $this->customerOptionValuePriceFactory->createFromConfig($valuePriceConfig);
            $valuePrice->setCustomerOptionValue($value);
            $valuePrice->setProduct($product);

            $prices[] = $valuePrice;
        }
        $product->setCustomerOptionValuePrices($prices);

        return $product;
    }
}
