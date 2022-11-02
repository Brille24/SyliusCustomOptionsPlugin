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

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValuePriceRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Exception;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class OrderItemOptionFactory implements OrderItemOptionFactoryInterface, FactoryInterface
{
    private FactoryInterface $factory;
    private CustomerOptionRepositoryInterface $customerOptionRepository;
    private CustomerOptionValueResolverInterface $valueResolver;
    private CustomerOptionValuePriceRepositoryInterface $customerOptionValuePriceRepository;

    public function __construct(
        FactoryInterface $factory,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueResolverInterface $valueResolver,
        CustomerOptionValuePriceRepositoryInterface $customerOptionValuePriceRepository
    ) {
        $this->factory                            = $factory;
        $this->customerOptionRepository           = $customerOptionRepository;
        $this->valueResolver                      = $valueResolver;
        $this->customerOptionValuePriceRepository = $customerOptionValuePriceRepository;
    }

    /** {@inheritdoc} */
    public function createNew(): object
    {
        return $this->factory->createNew();
    }

    /** {@inheritdoc} */
    public function createForOptionAndValue(
        OrderItemInterface $orderItem,
        CustomerOptionInterface $customerOption,
        $customerOptionValue
    ): OrderItemOptionInterface {
        /** @var OrderItemOptionInterface $orderItemOption */
        $orderItemOption = $this->createNew();

        $orderItemOption->setCustomerOption($customerOption);
        $orderItemOption->setCustomerOptionValue($customerOptionValue);

        if ($customerOptionValue instanceof CustomerOptionValueInterface) {
            /** @var OrderInterface $order */
            $order = $orderItem->getOrder();

            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();

            /** @var ChannelInterface $channel */
            $channel = $order->getChannel();

            /** @var CustomerOptionValuePriceInterface $price */
            $price   = $this->customerOptionValuePriceRepository->getPriceForChannel($channel, $product, $customerOptionValue);

            $orderItemOption->setPrice($price);
        }

        $orderItemOption->setOrderItem($orderItem);

        return $orderItemOption;
    }

    /** {@inheritdoc} */
    public function createNewFromStrings(
        OrderItemInterface $orderItem,
        string $customerOptionCode,
        $customerOptionValue
    ): OrderItemOptionInterface {
        $customerOption = $this->customerOptionRepository->findOneByCode($customerOptionCode);
        if ($customerOption === null) {
            throw new Exception('Could not find customer option with code');
        }

        if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
            $customerOptionValue = $this->valueResolver->resolve($customerOption, (string) $customerOptionValue);
        }

        return $this->createForOptionAndValue($orderItem, $customerOption, $customerOptionValue);
    }
}
