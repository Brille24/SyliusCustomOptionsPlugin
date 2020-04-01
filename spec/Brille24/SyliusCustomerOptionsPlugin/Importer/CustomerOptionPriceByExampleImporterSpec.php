<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConstraintViolationException;
use Brille24\SyliusCustomerOptionsPlugin\Handler\ImportErrorHandlerInterface;
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
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CustomerOptionPriceByExampleImporterSpec extends ObjectBehavior
{
    public function let(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ImportErrorHandlerInterface $importErrorHandler
    ): void {
        $this->beConstructedWith(
            $priceUpdater,
            $entityManager,
            $productRepository,
            $importErrorHandler
        );
    }

    public function it_uses_price_as_example_for_a_list_of_products(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionInterface $customerOption,
        ProductRepositoryInterface $productRepository,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct,
        ProductInterface $thirdProduct,
        ImportErrorHandlerInterface $importErrorHandler
    ): void {
        $productCodes = ['first', 'second', 'third'];

        $customerOptionValue->getCode()->shouldBeCalled()->willReturn('some_value');
        $customerOptionValue->getCustomerOption()->shouldBeCalled()->willReturn($customerOption);

        $customerOption->getCode()->shouldBeCalled()->willReturn('some_option');

        $channel->getCode()->shouldBeCalled()->willReturn('some_channel');

        $productRepository->findOneByCode('first')->willReturn($firstProduct);
        $productRepository->findOneByCode('second')->willReturn($secondProduct);
        $productRepository->findOneByCode('third')->willReturn($thirdProduct);

        $type = CustomerOptionValuePrice::TYPE_FIXED_AMOUNT;
        $amount = 1000;
        $percent = 0.0;

        $priceUpdater->updateForProduct(
            'some_option',
            'some_value',
            'some_channel',
            Argument::type(ProductInterface::class),
            null,
            null,
            $type,
            $amount,
            $percent
        )->shouldBeCalledTimes(3);

        $importErrorHandler->handleErrors([], Argument::type('array'))->shouldBeCalled();

        $this->importForProducts($productCodes, [$customerOptionValue], null, $channel, $type, $amount, $percent);
    }

    public function it_sends_mail_on_failed_import(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        CustomerOptionValueInterface $customerOptionValue,
        ChannelInterface $channel,
        CustomerOptionInterface $customerOption,
        ProductRepositoryInterface $productRepository,
        ProductInterface $firstProduct,
        ImportErrorHandlerInterface $importErrorHandler,
        ConstraintViolationListInterface $violationList,
        ConstraintViolationException $violationException
    ): void {
        $productCodes = ['first'];

        $customerOptionValue->getCode()->shouldBeCalled()->willReturn('some_value');
        $customerOptionValue->getCustomerOption()->shouldBeCalled()->willReturn($customerOption);

        $customerOption->getCode()->shouldBeCalled()->willReturn('some_option');

        $channel->getCode()->shouldBeCalled()->willReturn('some_channel');

        $productRepository->findOneByCode('first')->willReturn($firstProduct);

        $violationException->getViolations()->willReturn($violationList);

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
        )->shouldBeCalledTimes(1)->willThrow($violationException->getWrappedObject());

        $importErrorHandler->handleErrors(Argument::size(1), Argument::type('array'))->shouldBeCalled();

        $this->importForProducts($productCodes, [$customerOptionValue], null, $channel, CustomerOptionValuePrice::TYPE_FIXED_AMOUNT, 1000, 0.0);
    }
}
