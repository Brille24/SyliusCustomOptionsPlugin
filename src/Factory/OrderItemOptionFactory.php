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
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValuePriceRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Exception;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class OrderItemOptionFactory implements OrderItemOptionFactoryInterface, FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var CustomerOptionRepositoryInterface
     */
    private $customerOptionRepository;

    /**
     * @var CustomerOptionValueResolverInterface
     */
    private $valueResolver;

    /**
     * @var CustomerOptionValuePriceRepositoryInterface
     */
    private $customerOptionValuePriceRepository;

    public function __construct(
        FactoryInterface $factory,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueResolverInterface $valueResolver,
        CustomerOptionValuePriceRepositoryInterface $customerOptionValuePriceRepository
    ) {
        $this->factory = $factory;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->valueResolver = $valueResolver;
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

            /** @var ChannelInterface $channel */
            $channel = $order->getChannel();
            $price = $this->customerOptionValuePriceRepository->getPriceForChannel(
                $channel,
                $orderItem->getProduct(),
                $customerOptionValue
            );

            $orderItemOption->setPrice($price);
        }

        $orderItemOption->setOrderItem($orderItem);

        return $orderItemOption;
    }

    /** {@inheritdoc} */
    public function createNewFromStrings(
        OrderItemInterface $orderItem,
        string $customerOptionCode,
        string $customerOptionValue
    ): OrderItemOptionInterface {
        $customerOption = $this->customerOptionRepository->findOneByCode($customerOptionCode);
        if ($customerOption === null) {
            throw new Exception('Could not find customer option with code');
        }

        if (CustomerOptionTypeEnum::isSelect($customerOption->getType())) {
            $customerOptionValue = $this->valueResolver->resolve($customerOption, $customerOptionValue);
        }

        return $this->createForOptionAndValue($orderItem, $customerOption, $customerOptionValue);
    }
}
