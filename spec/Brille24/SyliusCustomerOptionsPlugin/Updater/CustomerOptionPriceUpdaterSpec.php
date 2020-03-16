<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Updater;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class CustomerOptionPriceUpdaterSpec extends ObjectBehavior
{
    public function let(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory
    ): void {
        $this->beConstructedWith(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $productRepository,
            $customerOptionValuePriceFactory
        );
    }

    public function it_updates_an_existing_price_with_product(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';
        $validFrom               = null;
        $validTo                 = null;
        $type                    = CustomerOptionValuePrice::TYPE_FIXED_AMOUNT;
        $amount                  = 500;
        $percent                 = 0.0;

        $this->setupMocks(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $productRepository,
            $customerOption,
            $customerOptionValue,
            $channel,
            $valuePrice,
            $product,
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        );
        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->findOneBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn($valuePrice);

        $expectedPrice = new CustomerOptionValuePrice();
        $expectedPrice->setProduct($product);
        $expectedPrice->setChannel($channel);
        $expectedPrice->setCustomerOptionValue($customerOptionValue);
        $expectedPrice->setPercent($percent);
        $expectedPrice->setAmount($amount);
        $expectedPrice->setType($type);
        $expectedPrice->setDateValid(null);

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBeLike($expectedPrice);
    }

    public function it_updates_an_existing_price_with_product_and_date(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';
        $validFrom               = '2020-01-01';
        $validTo                 = '2020-02-01';
        $type                    = CustomerOptionValuePrice::TYPE_FIXED_AMOUNT;
        $amount                  = 500;
        $percent                 = 0.0;

        $this->setupMocks(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $productRepository,
            $customerOption,
            $customerOptionValue,
            $channel,
            $valuePrice,
            $product,
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        );
        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->findOneBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn($valuePrice);


        $expectedPrice = new CustomerOptionValuePrice();
        $expectedPrice->setProduct($product);
        $expectedPrice->setChannel($channel);
        $expectedPrice->setCustomerOptionValue($customerOptionValue);
        $expectedPrice->setPercent($percent);
        $expectedPrice->setAmount($amount);
        $expectedPrice->setType($type);
        $expectedPrice->setDateValid(new DateRange(new \DateTime($validFrom), new \DateTime($validTo)));

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBeLike($expectedPrice);
    }

    public function it_updates_a_new_price_with_product(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';
        $validFrom               = null;
        $validTo                 = null;
        $type                    = CustomerOptionValuePrice::TYPE_FIXED_AMOUNT;
        $amount                  = 500;
        $percent                 = 0.0;

        $this->setupMocks(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $productRepository,
            $customerOption,
            $customerOptionValue,
            $channel,
            $valuePrice,
            $product,
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        );
        $customerOptionValuePriceFactory->createNew()->shouldBeCalled()->willReturn($valuePrice);
        $valuePrice->setCustomerOptionValue($customerOptionValue)->shouldBeCalled();
        $valuePrice->setChannel($channel)->shouldBeCalled();
        $valuePrice->setProduct($product)->shouldBeCalled();

        $customerOptionValuePriceRepository->findOneBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn(null);

        $expectedPrice = new CustomerOptionValuePrice();
        $expectedPrice->setProduct($product);
        $expectedPrice->setChannel($channel);
        $expectedPrice->setCustomerOptionValue($customerOptionValue);
        $expectedPrice->setPercent($percent);
        $expectedPrice->setAmount($amount);
        $expectedPrice->setType($type);
        $expectedPrice->setDateValid(null);

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBeLike($expectedPrice);
    }

    public function it_updates_a_new_price_with_product_and_date(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';
        $validFrom               = '2020-01-01';
        $validTo                 = '2020-02-01';
        $type                    = CustomerOptionValuePrice::TYPE_FIXED_AMOUNT;
        $amount                  = 500;
        $percent                 = 0.0;

        $this->setupMocks(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $productRepository,
            $customerOption,
            $customerOptionValue,
            $channel,
            $valuePrice,
            $product,
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        );
        $customerOptionValuePriceFactory->createNew()->shouldBeCalled()->willReturn($valuePrice);
        $valuePrice->setCustomerOptionValue($customerOptionValue)->shouldBeCalled();
        $valuePrice->setChannel($channel)->shouldBeCalled();
        $valuePrice->setProduct($product)->shouldBeCalled();

        $customerOptionValuePriceRepository->findOneBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn(null);

        $expectedPrice = new CustomerOptionValuePrice();
        $expectedPrice->setProduct($product);
        $expectedPrice->setChannel($channel);
        $expectedPrice->setCustomerOptionValue($customerOptionValue);
        $expectedPrice->setPercent($percent);
        $expectedPrice->setAmount($amount);
        $expectedPrice->setType($type);
        $expectedPrice->setDateValid(new DateRange(new \DateTime($validFrom), new \DateTime($validTo)));

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBeLike($expectedPrice);
    }

    public function it_requires_the_customer_option_value_to_exist(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        CustomerOptionInterface $customerOption
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';

        $customerOptionRepository->findOneByCode($customerOptionCode)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            null,
            null,
            CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
            0,
            0.0,
        ]);
    }

    public function it_requires_the_channel_to_exist(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelRepositoryInterface $channelRepository
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';

        $customerOptionRepository->findOneByCode($customerOptionCode)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ])->willReturn($customerOptionValue);

        $channelRepository->findOneByCode($channelCode)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            null,
            null,
            CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
            0,
            0.0,
        ]);
    }

    public function it_requires_the_product_to_exist(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        ProductRepositoryInterface $productRepository
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';
        $productCode             = 'some product';

        $customerOptionRepository->findOneByCode($customerOptionCode)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ])->willReturn($customerOptionValue);

        $channelRepository->findOneByCode($channelCode)->willReturn($channel);
        $productRepository->findOneByCode($productCode)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $productCode,
            null,
            null,
            CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
            0,
            0.0,
        ]);
    }

    /**
     * @param CustomerOptionRepositoryInterface $customerOptionRepository
     * @param RepositoryInterface $customerOptionValuePriceRepository
     * @param CustomerOptionValueRepositoryInterface $customerOptionValueRepository
     * @param ChannelRepositoryInterface $channelRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerOptionInterface $customerOption
     * @param CustomerOptionValueInterface $customerOptionValue
     * @param ChannelInterface $channel
     * @param CustomerOptionValuePriceInterface $valuePrice
     * @param ProductInterface|null $product
     * @param string $customerOptionCode
     * @param string $customerOptionValueCode
     * @param string $channelCode
     * @param string $productCode
     * @param string $validFrom
     * @param string $validTo
     * @param string $type
     * @param int $amount
     * @param float $percent
     */
    private function setupMocks(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,

        CustomerOptionValuePriceInterface $valuePrice,
        // phpspec throws an exception if we make parameters nullable, therefore $product has no specific type.
        $product,

        string $customerOptionCode,
        string $customerOptionValueCode,
        string $channelCode,
        string $productCode,
        string $validFrom,
        string $validTo,
        string $type,
        int $amount,
        float $percent
    ): void {
        $customerOptionRepository->findOneByCode($customerOptionCode)->shouldBeCalled()->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ])->shouldBeCalled()->willReturn($customerOptionValue);

        $channelRepository->findOneByCode($channelCode)->shouldBeCalled()->willReturn($channel);
        $productRepository->findOneByCode($productCode)->willReturn($product);

        if (!empty($validFrom) && !empty($validTo)) {
            $valuePrice->setDateValid(Argument::type(DateRange::class))->shouldBeCalled();
        } else {
            $valuePrice->setDateValid(Argument::any())->shouldNotBeCalled();
        }
        $valuePrice->setType($type)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setPercent($percent)->shouldBeCalled();
    }
}
