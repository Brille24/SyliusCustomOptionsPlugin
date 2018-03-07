<?php
declare(strict_types=1);
/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brille24\CustomerOptionsPlugin\Factory;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\CustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\CustomerOptionsPlugin\Exceptions\ConfigurationException;
use Brille24\CustomerOptionsPlugin\Services\FakerFactoryWrapper;
use Doctrine\ORM\EntityNotFoundException;
use Faker\Factory;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

class CustomerOptionValuePriceFactory implements CustomerOptionFactoryInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var FakerFactoryWrapper
     */
    private $faker;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
        $this->faker             = Factory::create();
    }

    /** {@inheritdoc} */
    public function validateConfiguration(array $configuration): void
    {
        ConfigurationException::createFromMissingArrayKey('type', $configuration);
        ConfigurationException::createFromArrayContains($configuration['type'], ['fixed', 'percent']);
        if ($configuration['type'] === 'fixed') {
            ConfigurationException::createFromMissingArrayKey('amount', $configuration);
        } else {
            ConfigurationException::createFromMissingArrayKey('percent', $configuration);
        }

        ConfigurationException::createFromMissingArrayKey('channel', $configuration);
    }

    /** {@inheritdoc} */
    public function create(array $configuration): CustomerOptionValuePriceInterface
    {
        $this->validateConfiguration($configuration);

        $price = new CustomerOptionValuePrice();

        switch ($configuration['type']) {
            case 'fixed':
                $price->setType(CustomerOptionValuePrice::TYPE_FIXED_AMOUNT);
                $price->setAmount(intval($configuration['amount']));
                break;

            case 'percent':
                $price->setType(CustomerOptionValuePrice::TYPE_PERCENT);
                $price->setPercent(floatval($configuration['percent']));
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($configuration['channel']);

        if ($channel === null) {
            throw new EntityNotFoundException('Could not find Channel with code: "' . $configuration['channel'] . '"');
        }

        $price->setChannel($channel);

        return $price;
    }

    /** {@inheritdoc} */
    public function generateRandomConfiguration(int $amount): array
    {
        $prices          = [];
        $allChannelCodes = array_map(
            function (ChannelInterface $channel) { return $channel->getCode(); },
            $this->channelRepository->findAll()
        );

        foreach (range(1, $amount) as $i) {
            $price = [
                'type'    => $this->faker->randomElement(['fixed', 'percent']),
                'amount'  => $this->faker->numberBetween(50, 10000),
                'percent' => $this->faker->randomFloat(4, 0.01, 0.5),
                'channel' => $this->faker->randomElement($allChannelCodes),
            ];

            $prices[] = $price;
        }

        return $prices;
    }
}