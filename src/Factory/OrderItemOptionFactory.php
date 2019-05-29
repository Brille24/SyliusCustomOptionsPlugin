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
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Exception;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class OrderItemOptionFactory implements OrderItemOptionFactoryInterface, FactoryInterface
{
    /**
     * @var ChannelInterface
     */
    private $channel;

    /**
     * @var CustomerOptionRepositoryInterface
     */
    private $customerOptionRepository;

    /**
     * @var CustomerOptionValueResolverInterface
     */
    private $valueResolver;

    public function __construct(
        ChannelInterface $channel,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueResolverInterface $valueResolver
    ) {
        $this->channel                  = $channel;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->valueResolver            = $valueResolver;
    }

    public function createNew()
    {
        throw new Exception("Please implement this with proper default values!");
        return $orderItemOption;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function createForOptionAndValue(CustomerOptionInterface $customerOption, $customerOptionValue): OrderItemOptionInterface
    {
        return new OrderItemOption($this->channel, $customerOption, $customerOptionValue);
    }

    /** {@inheritdoc} */
    public function createNewFromStrings(
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

        return $this->createNew($customerOption, $customerOptionValue);
    }
}
