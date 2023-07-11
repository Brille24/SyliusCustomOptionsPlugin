<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Repository;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerOptionValuePriceRepositoryInterface extends RepositoryInterface
{
    public function getPriceForChannel(
        ChannelInterface $channel,
        ProductInterface $product,
        CustomerOptionValueInterface $customerOptionValue,
    ): ?CustomerOptionValuePriceInterface;
}
