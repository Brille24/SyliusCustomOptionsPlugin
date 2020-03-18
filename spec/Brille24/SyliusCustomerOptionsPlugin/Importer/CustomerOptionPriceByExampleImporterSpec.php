<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CustomerOptionPriceByExampleImporterSpec extends ObjectBehavior
{
    public function let(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        SenderInterface $sender,
        TokenStorageInterface $tokenStorage,
        ProductRepositoryInterface $productRepository
    ): void {
        $this->beConstructedWith($priceUpdater, $entityManager, $sender, $tokenStorage, $productRepository);
    }

    public function it_uses_price_as_example_for_a_list_of_products(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        CustomerOptionValuePriceInterface $valuePrice,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionInterface $customerOption,
        ProductRepositoryInterface $productRepository,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct,
        ProductInterface $thirdProduct
    ): void {
        $productCodes = ['first', 'second', 'third'];

        $valuePrice->getCustomerOptionValue()->shouldBeCalled()->willReturn($customerOptionValue);
        $valuePrice->getDateValid()->shouldBeCalled()->willReturn(null);
        $valuePrice->getPercent()->shouldBeCalled()->willReturn(0.0);
        $valuePrice->getAmount()->shouldBeCalled()->willReturn(1000);
        $valuePrice->getType()->shouldBeCalled()->willReturn(CustomerOptionValuePrice::TYPE_FIXED_AMOUNT);
        $valuePrice->getChannel()->shouldBeCalled()->willReturn($channel);

        $customerOptionValue->getCode()->shouldBeCalled()->willReturn('some_value');
        $customerOptionValue->getCustomerOption()->shouldBeCalled()->willReturn($customerOption);

        $customerOption->getCode()->shouldBeCalled()->willReturn('some_option');

        $channel->getCode()->shouldBeCalled()->willReturn('some_channel');

        $productRepository->findOneByCode('first')->willReturn($firstProduct);
        $productRepository->findOneByCode('second')->willReturn($secondProduct);
        $productRepository->findOneByCode('third')->willReturn($thirdProduct);

        $priceUpdater->updateForProduct(
            'some_option',
            'some_value',
            'some_channel',
            Argument::type(ProductInterface::class),
            null,
            null,
            CustomerOptionValuePrice::TYPE_FIXED_AMOUNT,
            1000,
            0.0
        )->shouldBeCalledTimes(3);

        $this->importForProducts($productCodes, $valuePrice);
    }

    public function it_sends_mail_on_failed_import(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        CustomerOptionValuePriceInterface $valuePrice,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionInterface $customerOption,
        SenderInterface $sender,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $adminUser,
        ProductRepositoryInterface $productRepository,
        ProductInterface $firstProduct
    ): void {
        $productCodes = ['first'];

        $valuePrice->getCustomerOptionValue()->shouldBeCalled()->willReturn($customerOptionValue);
        $valuePrice->getDateValid()->shouldBeCalled()->willReturn(null);
        $valuePrice->getPercent()->shouldBeCalled()->willReturn(0.0);
        $valuePrice->getAmount()->shouldBeCalled()->willReturn(1000);
        $valuePrice->getType()->shouldBeCalled()->willReturn(CustomerOptionValuePrice::TYPE_FIXED_AMOUNT);
        $valuePrice->getChannel()->shouldBeCalled()->willReturn($channel);

        $customerOptionValue->getCode()->shouldBeCalled()->willReturn('some_value');
        $customerOptionValue->getCustomerOption()->shouldBeCalled()->willReturn($customerOption);

        $customerOption->getCode()->shouldBeCalled()->willReturn('some_option');

        $channel->getCode()->shouldBeCalled()->willReturn('some_channel');

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($adminUser);
        $adminUser->getEmail()->willReturn('john.doe@example.com');

        $productRepository->findOneByCode('first')->willReturn($firstProduct);

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            null,
            null,
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldBeCalledTimes(1)->willThrow(\InvalidArgumentException::class);

        $sender->send(Argument::type('string'), ['john.doe@example.com'], Argument::type('array'))->shouldBeCalled();

        $this->importForProducts($productCodes, $valuePrice);
    }
}
