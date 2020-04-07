<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerOptionPriceImporterSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ValidatorInterface $validator,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,

        ProductInterface $firstProduct,
        ProductInterface $secondProduct,
        CustomerOptionInterface $customerOption,
        CustomerOptionValueInterface $someValue,
        CustomerOptionValueInterface $otherValue,
        ChannelInterface $someChannel,
        ChannelInterface $otherChannel
    ): void {
        $this->beConstructedWith(
            $entityManager,
            $productRepository,
            $validator,
            $customerOptionRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $customerOptionValuePriceRepository,
            $customerOptionValuePriceFactory
        );

        $productRepository->findOneByCode('first_product')->shouldBeCalled()->willReturn($firstProduct);
        $productRepository->findOneByCode('second_product')->shouldBeCalled()->willReturn($secondProduct);
        $customerOptionRepository->findOneByCode('some_option')->shouldBeCalled()->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy(['code' => 'some_value', 'customerOption' => $customerOption])->shouldBeCalled()->willReturn($someValue);
        $customerOptionValueRepository->findOneBy(['code' => 'other_value', 'customerOption' => $customerOption])->shouldBeCalled()->willReturn($otherValue);
        $channelRepository->findOneByCode('some_channel')->shouldBeCalled()->willReturn($someChannel);
        $channelRepository->findOneByCode('other_channel')->shouldBeCalled()->willReturn($otherChannel);
        $customerOptionValuePriceRepository->findBy(Argument::type('array'))->shouldBeCalled()->willReturn([]);
    }

    public function it_creates_new_prices(
        EntityManagerInterface $entityManager,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValuePriceInterface $valuePrice,
        ValidatorInterface $validator,
        ConstraintViolationListInterface $violationList
    ): void {
        $customerOptionValuePriceFactory->createNew()->shouldBeCalledTimes(3)->willReturn($valuePrice);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldBeCalledTimes(3);
        $entityManager->flush()->shouldBeCalled();

        $validator->validate(Argument::type(ProductInterface::class), null, 'sylius')->shouldBeCalledTimes(3)->willReturn($violationList);
        $violationList->count()->shouldBeCalledTimes(3)->willReturn(0);

        $this->import($this->getData());
    }

    public function it_updates_existing_prices(
        EntityManagerInterface $entityManager,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValuePriceInterface $valuePrice,
        ValidatorInterface $validator,
        ConstraintViolationListInterface $violationList
    ): void {
        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->findBy(Argument::type('array'))->shouldBeCalledTimes(3)->willReturn([$valuePrice]);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldBeCalledTimes(3);
        $entityManager->flush()->shouldBeCalled();

        $validator->validate(Argument::type(ProductInterface::class), null, 'sylius')->shouldBeCalledTimes(3)->willReturn($violationList);
        $violationList->count()->shouldBeCalledTimes(3)->willReturn(0);

        $this->import($this->getData());
    }

    public function it_returns_import_errors(
        EntityManagerInterface $entityManager,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValuePriceInterface $valuePrice,
        ValidatorInterface $validator,
        ConstraintViolationListInterface $violationList
    ): void {
        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->findBy(Argument::type('array'))->shouldBeCalledTimes(3)->willReturn([$valuePrice]);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldNotBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $validator->validate(Argument::type(ProductInterface::class), null, 'sylius')->shouldBeCalledTimes(3)->willReturn($violationList);
        $violationList->count()->shouldBeCalledTimes(3)->willReturn(1);

        $expected = ['imported' => 0, 'failed' => [[
                'violations' => $violationList,
                'data'       => $this->getData()[0],
                'message'    => '',
            ],
            [
                'violations' => $violationList,
                'data'       => $this->getData()[1],
                'message'    => '',
            ],
            [
                'violations' => $violationList,
                'data'       => $this->getData()[2],
                'message'    => '',
            ],
        ]];

        $this->import($this->getData())->shouldBeLike($expected);
    }

    private function getData(): array
    {
        return [
            [
                'product_code'               => 'first_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'some_value',
                'channel_code'               => 'some_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
            ],
            [
                'product_code'               => 'second_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'other_value',
                'channel_code'               => 'some_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
            ],
            [
                'product_code'               => 'first_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'some_value',
                'channel_code'               => 'other_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
            ],
        ];
    }
}
