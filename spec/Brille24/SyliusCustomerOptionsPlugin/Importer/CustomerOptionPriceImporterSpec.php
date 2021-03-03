<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Object\PriceImportResult;
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

        $productRepository->findOneByCode('first_product')->willReturn($firstProduct);
        $productRepository->findOneByCode('second_product')->willReturn($secondProduct);
        $customerOptionRepository->findOneByCode('some_option')->willReturn($customerOption);
        $customerOptionValueRepository->findOneBy(['code' => 'some_value', 'customerOption' => $customerOption])->willReturn($someValue);
        $customerOptionValueRepository->findOneBy(['code' => 'other_value', 'customerOption' => $customerOption])->willReturn($otherValue);
        $channelRepository->findOneByCode('some_channel')->willReturn($someChannel);
        $channelRepository->findOneByCode('other_channel')->willReturn($otherChannel);
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

        $this->import($this->getCreateData())->shouldBeLike(new PriceImportResult(3, 0, []));
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

        $customerOptionValuePriceRepository->find(10)->shouldBeCalledOnce()->willReturn($valuePrice);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalled();

        $validator->validate(Argument::type(ProductInterface::class), null, 'sylius')->shouldBeCalledOnce()->willReturn($violationList);
        $violationList->count()->shouldBeCalledOnce()->willReturn(0);

        $this->import($this->getUpdateData())->shouldBeLike(new PriceImportResult(1, 0, []));
    }

    public function it_deletes_existing_prices(
        EntityManagerInterface $entityManager,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductInterface $firstProduct
    ): void {
        $customerOptionValuePriceFactory->createNew()->shouldNotBeCalled();

        $customerOptionValuePriceRepository->find(10)->shouldBeCalledOnce()->willReturn($valuePrice);

        $firstProduct->removeCustomerOptionValuePrice($valuePrice)->shouldBeCalledOnce();
        $entityManager->persist($firstProduct)->shouldBeCalledOnce();
        $entityManager->flush()->shouldBeCalled();

        $this->import($this->getDeleteData())->shouldBeLike(new PriceImportResult(1, 0, []));
    }

    public function it_returns_import_errors(
        EntityManagerInterface $entityManager,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        CustomerOptionValuePriceInterface $valuePrice,
        ValidatorInterface $validator,
        ConstraintViolationListInterface $violationList,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $customerOptionValuePriceRepository->find(Argument::any())->shouldNotBeCalled();
        $customerOptionValuePriceFactory->createNew()->shouldBeCalledTimes(3)->willReturn($valuePrice);

        $firstProduct->addCustomerOptionValuePrice($valuePrice)->shouldBeCalledTimes(2);
        $secondProduct->addCustomerOptionValuePrice($valuePrice)->shouldBeCalledOnce();

        $firstProduct->removeCustomerOptionValuePrice($valuePrice)->shouldBeCalledTimes(2);
        $secondProduct->removeCustomerOptionValuePrice($valuePrice)->shouldBeCalledOnce();

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldNotBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $validator->validate(Argument::type(ProductInterface::class), null, 'sylius')->shouldBeCalledTimes(3)->willReturn($violationList);
        $violationList->count()->shouldBeCalledTimes(3)->willReturn(1);

        $expected = new PriceImportResult(0, 3, [
            'first_product' => [
                [
                    'violations' => $violationList->getWrappedObject(),
                    'data'       => $this->getCreateData()[0],
                    'message'    => '',
                ],
                [
                    'violations' => $violationList->getWrappedObject(),
                    'data'       => $this->getCreateData()[2],
                    'message'    => '',
                ]
            ],
            'second_product' => [[
                'violations' => $violationList->getWrappedObject(),
                'data'       => $this->getCreateData()[1],
                'message'    => '',
            ]],
        ]);

        $this->import($this->getCreateData())->shouldBeLike($expected);
    }

    private function getCreateData(): array
    {
        return [
            [
                'id'                         => null,
                'product_code'               => 'first_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'some_value',
                'channel_code'               => 'some_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
                'delete'                     => 0,
            ],
            [
                'id'                         => null,
                'product_code'               => 'second_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'other_value',
                'channel_code'               => 'some_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
                'delete'                     => 0,
            ],
            [
                'id'                         => null,
                'product_code'               => 'first_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'some_value',
                'channel_code'               => 'other_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
                'delete'                     => 0,
            ],
        ];
    }

    private function getUpdateData(): array
    {
        return [
            [
                'id'                         => 10,
                'product_code'               => 'first_product',
                'customer_option_code'       => 'some_option',
                'customer_option_value_code' => 'some_value',
                'channel_code'               => 'some_channel',
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
                'amount'                     => 100,
                'percent'                    => 0.0,
                'delete'                     => 0,
            ],
        ];
    }

    private function getDeleteData(): array
    {
        return [
            [
                'id'                         => 10,
                'product_code'               => 'first_product',
                'customer_option_code'       => null,
                'customer_option_value_code' => null,
                'channel_code'               => null,
                'valid_from'                 => null,
                'valid_to'                   => null,
                'type'                       => null,
                'amount'                     => null,
                'percent'                    => null,
                'delete'                     => 1,
            ],
        ];
    }
}
