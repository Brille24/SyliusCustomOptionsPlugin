<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Sylius\Component\Core\Model\ChannelInterface;

interface CustomerOptionPriceByExampleImporterInterface
{
    /**
     * @param string[] $productCodes
     * @param CustomerOptionValuePriceInterface[] $customerOptionValues
     * @param DateRange|null $dateValid
     * @param ChannelInterface $channel
     * @param string $type
     * @param int $amount
     * @param float $percent
     *
     * @return array
     */
    public function importForProducts(
        array $productCodes,
        array $customerOptionValues,
        ?DateRange $dateValid,
        ChannelInterface $channel,
        string $type,
        int $amount,
        float $percent
    ): array;
}
