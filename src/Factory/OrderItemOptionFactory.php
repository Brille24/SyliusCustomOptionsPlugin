<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\CustomerOptionsPlugin\Services\CustomerOptionValueResolverInterface;
use Exception;
use Sylius\Component\Core\Model\ChannelInterface;

class OrderItemOptionFactory implements OrderItemOptionFactoryInterface
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
        $this->channel = $channel;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->valueResolver = $valueResolver;
    }

    /** {@inheritdoc} */
    public function createNew(CustomerOptionInterface $customerOption, $customerOptionValue): OrderItemOptionInterface
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
