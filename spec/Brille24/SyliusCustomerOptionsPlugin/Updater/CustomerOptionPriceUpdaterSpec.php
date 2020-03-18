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
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionValuePriceDateOverlapConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerOptionPriceUpdaterSpec extends ObjectBehavior
{
    public function let(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        ValidatorInterface $validator
    ): void {
        $this->beConstructedWith(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $customerOptionValuePriceFactory,
            $validator
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
        ProductInterface $product,
        ValidatorInterface $validator
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

        $valuePrice->getDateValid()->shouldBeCalled()->willReturn(null);

        $this->setupMocks(
            $customerOptionRepository,
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

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([$valuePrice]);

        $valuePrice->setPercent($percent)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setType($type)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection([$valuePrice]));
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $product,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBe($valuePrice);
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
        ProductInterface $product,
        ValidatorInterface $validator
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

        $valuePrice->getDateValid()->shouldBeCalled()->willReturn(new DateRange(new \DateTime($validFrom), new \DateTime($validTo)));

        $this->setupMocks(
            $customerOptionRepository,
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

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([$valuePrice]);

        $valuePrice->setPercent($percent)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setType($type)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection([$valuePrice]));
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $product,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBe($valuePrice);
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
        ProductInterface $product,
        ValidatorInterface $validator
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

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([]);

        $valuePrice->setProduct($product)->shouldBeCalled();
        $valuePrice->setChannel($channel)->shouldBeCalled();
        $valuePrice->setCustomerOptionValue($customerOptionValue)->shouldBeCalled();
        $valuePrice->setPercent($percent)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setType($type)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection());
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $product,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBe($valuePrice);
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
        ProductInterface $product,
        ValidatorInterface $validator
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

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([]);

        $valuePrice->setProduct($product)->shouldBeCalled();
        $valuePrice->setChannel($channel)->shouldBeCalled();
        $valuePrice->setCustomerOptionValue($customerOptionValue)->shouldBeCalled();
        $valuePrice->setPercent($percent)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setType($type)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection());
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $product,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent
        )->shouldBe($valuePrice);
    }

    public function it_requires_the_customer_option_value_to_exist(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        CustomerOptionInterface $customerOption,
        ProductInterface $product
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';

        $customerOptionRepository->findOneByCode($customerOptionCode)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $product,
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
        ChannelRepositoryInterface $channelRepository,
        ProductInterface $product
    ): void {
        $customerOptionCode      = 'some option';
        $customerOptionValueCode = 'some value';
        $channelCode             = 'some channel';

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
            $product,
            null,
            null,
            CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
            0,
            0.0,
        ]);
    }

    public function it_throws_exception_if_validation_fails(
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
        ProductInterface $product,
        ValidatorInterface $validator,
        ConstraintViolationInterface $violation
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

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([]);

        $valuePrice->setProduct($product)->shouldBeCalled();
        $valuePrice->setChannel($channel)->shouldBeCalled();
        $valuePrice->setCustomerOptionValue($customerOptionValue)->shouldBeCalled();
        $valuePrice->setPercent($percent)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setType($type)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection());
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([$violation]);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            $customerOptionCode,
            $customerOptionValueCode,
            $channelCode,
            $product,
            $validFrom,
            $validTo,
            $type,
            $amount,
            $percent,
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
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,

        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product,

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
            $valuePrice->setDateValid(null)->shouldBeCalled();
        }
        $valuePrice->setType($type)->shouldBeCalled();
        $valuePrice->setAmount($amount)->shouldBeCalled();
        $valuePrice->setPercent($percent)->shouldBeCalled();
    }
}
