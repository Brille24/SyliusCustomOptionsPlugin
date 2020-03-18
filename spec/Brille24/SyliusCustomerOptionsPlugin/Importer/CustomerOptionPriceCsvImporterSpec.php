<?php

declare(strict_types=1);

namespace spec\Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Updater\CustomerOptionPriceUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CustomerOptionPriceCsvImporterSpec extends ObjectBehavior
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

    public function it_updates_prices(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        CustomerOptionValuePriceInterface $valuePrice,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product
    ): void {
        $productRepository->findOneByCode(Argument::type('string'))->willReturn($product);

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldBeCalledTimes(2)->willReturn($valuePrice);

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
        )->shouldBeCalledTimes(1)->willReturn($valuePrice);

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldBeCalledTimes(3);
        $entityManager->flush()->shouldBeCalled();

        $this->import($this->getValidCsvFile());
    }

    public function it_sends_mail_on_failed_import(
        CustomerOptionPriceUpdaterInterface $priceUpdater,
        EntityManagerInterface $entityManager,
        SenderInterface $sender,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $adminUser,
        ProductRepositoryInterface $productRepository
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($adminUser);
        $adminUser->getEmail()->willReturn('john.doe@example.com');

        $productRepository->findOneByCode(Argument::type('string'))->willThrow(\InvalidArgumentException::class);

        $priceUpdater->updateForProduct(
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type(ProductInterface::class),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('string'),
            Argument::type('int'),
            Argument::type('float')
        )->shouldNotBeCalled();

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
        )->shouldNotBeCalled();

        $sender->send(Argument::type('string'), ['john.doe@example.com'], Argument::type('array'), Argument::type('array'))->shouldBeCalled();

        $entityManager->persist(Argument::type(CustomerOptionValuePriceInterface::class))->shouldNotBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->import($this->getInValidCsvFile());
    }

    /**
     * @return string
     */
    private function getValidCsvFile(): string
    {
        $csvString = <<<EOT
product_code,customer_option_code,customer_option_value_code,channel_code,type,amount,percent,valid_from,valid_to
first_product,lens_type,none,glasses24,FIXED_AMOUNT,1000,0.0,,
first_product,lens_type,none,glasses24,FIXED_AMOUNT,1500,0.0,2020-01-01,2020-02-31
second_product,lens_type,none,glasses24,FIXED_AMOUNT,1500,0.0,2020-01-01,2020-02-31
EOT;

        $filePath = @tempnam(sys_get_temp_dir(), 'cop');

        file_put_contents($filePath, $csvString);

        return $filePath;
    }

    /**
     * @return string
     */
    private function getInValidCsvFile(): string
    {
        $csvString = <<<EOT
product_code,customer_option_code,customer_option_value_code,channel_code,type,amount,percent,valid_from,valid_to
first_prodt,lens_type,none,glasses24,FIXED_AMOUNT,1000,0.0,,
first_product,,none,glasses24,FIXED_AMOUNT,1500,0.0,2020-01-01,2020-02-31
second_product,lens_type,none,glasses2,FIXED_AMOUNT,1500,0.0,2020-01-01,2020-02-31
EOT;

        $filePath = @tempnam(sys_get_temp_dir(), 'cop');

        file_put_contents($filePath, $csvString);

        return $filePath;
    }
}
