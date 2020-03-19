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
    private const CUSTOMER_OPTION_CODE       = 'color';
    private const CUSTOMER_OPTION_VALUE_CODE = 'red';
    private const CHANNEL_CODE               = 'US_WEB';
    private const PRODUCT_CODE               = 'tshirt';
    private const PRICE_TYPE                 = CustomerOptionValuePrice::TYPE_FIXED_AMOUNT;
    private const PRICE_AMOUNT               = 1000;
    private const PRICE_PERCENT              = 0.0;

    public function let(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        ValidatorInterface $validator,

        ProductRepositoryInterface $productRepository,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product
    ): void {
        $this->beConstructedWith(
            $customerOptionRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $customerOptionValuePriceFactory,
            $validator
        );

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
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            self::PRODUCT_CODE,
            '',
            '',
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT
        );
    }

    public function it_updates_an_existing_price_with_product(
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product,
        ValidatorInterface $validator
    ): void {
        $validFrom = null;
        $validTo   = null;

        $valuePrice->getDateValid()->shouldBeCalled()->willReturn(null);

        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([$valuePrice]);

        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection([$valuePrice]));
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            $validFrom,
            $validTo,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT
        )->shouldBe($valuePrice);
    }

    public function it_updates_an_existing_price_with_product_and_date(
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product,
        ValidatorInterface $validator
    ): void {
        $validFrom = '2020-01-01';
        $validTo   = '2020-02-01';

        $valuePrice->getDateValid()->shouldBeCalled()->willReturn(new DateRange(new \DateTime($validFrom), new \DateTime($validTo)));
        $valuePrice->setDateValid(Argument::type(DateRange::class))->shouldBeCalled();
        $valuePrice->setDateValid(null)->shouldNotBeCalled();

        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ])->shouldBeCalled()->willReturn([$valuePrice]);

        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection([$valuePrice]));
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            $validFrom,
            $validTo,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT
        )->shouldBe($valuePrice);
    }

    public function it_updates_a_new_price_with_product(
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product,
        ValidatorInterface $validator
    ): void {
        $validFrom = null;
        $validTo   = null;

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
        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection());
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            $validFrom,
            $validTo,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT
        )->shouldBe($valuePrice);
    }

    public function it_updates_a_new_price_with_product_and_date(
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product,
        ValidatorInterface $validator
    ): void {
        $validFrom = '2020-01-01';
        $validTo   = '2020-02-01';

        $valuePrice->setDateValid(Argument::type(DateRange::class))->shouldBeCalled();
        $valuePrice->setDateValid(null)->shouldNotBeCalled();

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
        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection());
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([]);

        $this->updateForProduct(
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            $validFrom,
            $validTo,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT
        )->shouldBe($valuePrice);
    }

    public function it_requires_the_customer_option_value_to_exist(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        CustomerOptionInterface $customerOption,
        ProductInterface $product,
        CustomerOptionValuePriceInterface $valuePrice
    ): void {
        // Undo the predictions defined during the let() method.
        $valuePrice->setDateValid(null)->shouldNotBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldNotBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldNotBeCalled();
        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldNotBeCalled();

        $customerOptionRepository->findOneByCode(self::CUSTOMER_OPTION_CODE)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => self::CUSTOMER_OPTION_VALUE_CODE,
            'customerOption' => $customerOption,
        ])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            null,
            null,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT,
        ]);
    }

    public function it_requires_the_channel_to_exist(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelRepositoryInterface $channelRepository,
        ProductInterface $product,
        CustomerOptionValuePriceInterface $valuePrice
    ): void {
        // Undo the predictions defined during the let() method.
        $valuePrice->setDateValid(null)->shouldNotBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldNotBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldNotBeCalled();
        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldNotBeCalled();

        $customerOptionRepository->findOneByCode(self::CUSTOMER_OPTION_CODE)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => self::CUSTOMER_OPTION_VALUE_CODE,
            'customerOption' => $customerOption,
        ])->willReturn($customerOptionValue);

        $channelRepository->findOneByCode(self::CHANNEL_CODE)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            null,
            null,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT,
        ]);
    }

    public function it_throws_exception_if_validation_fails(
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $product,
        ValidatorInterface $validator,
        ConstraintViolationInterface $violation
    ): void {
        $validFrom = '2020-01-01';
        $validTo   = '2020-02-01';

        $valuePrice->setDateValid(Argument::type(DateRange::class))->shouldBeCalled();
        $valuePrice->setDateValid(null)->shouldNotBeCalled();

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
        $valuePrice->setPercent(self::PRICE_PERCENT)->shouldBeCalled();
        $valuePrice->setAmount(self::PRICE_AMOUNT)->shouldBeCalled();
        $valuePrice->setType(self::PRICE_TYPE)->shouldBeCalled();

        $product->getCustomerOptionValuePrices()->willReturn(new ArrayCollection());
        $validator->validate(
            Argument::type(Collection::class),
            Argument::type(CustomerOptionValuePriceDateOverlapConstraint::class)
        )->shouldBeCalled()->willReturn([$violation]);

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateForProduct', [
            self::CUSTOMER_OPTION_CODE,
            self::CUSTOMER_OPTION_VALUE_CODE,
            self::CHANNEL_CODE,
            $product,
            $validFrom,
            $validTo,
            self::PRICE_TYPE,
            self::PRICE_AMOUNT,
            self::PRICE_PERCENT,
        ]);
    }

    /**
     * @param CustomerOptionRepositoryInterface $customerOptionRepository
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
        $customerOptionRepository->findOneByCode($customerOptionCode)->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ])->willReturn($customerOptionValue);

        $channelRepository->findOneByCode($channelCode)->willReturn($channel);
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
