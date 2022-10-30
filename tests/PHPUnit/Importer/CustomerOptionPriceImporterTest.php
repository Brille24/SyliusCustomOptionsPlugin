<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePrice;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Importer\CustomerOptionPriceImporter;
use Brille24\SyliusCustomerOptionsPlugin\Object\PriceImportResult;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValuePriceRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerOptionPriceImporterTest extends TestCase
{
    /** @var EntityManagerInterface|MockObject */
    private $entityManager;

    private CustomerOptionPriceImporter $customerOptionPriceImporter;

    /** @var MockObject|ValidatorInterface */
    private $validator;

    /** @var MockObject|CustomerOptionValuePriceFactoryInterface */
    private $customerOptionValuePriceFactory;

    /** @var MockObject|CustomerOptionValuePriceRepositoryInterface */
    private $customerOptionValuePriceRepository;

    /** @var CustomerOptionInterface|MockObject */
    private $customerOption;

    /** @var ProductInterface|MockObject */
    private $firstProduct;

    /** @var ProductInterface|MockObject */
    private $secondProduct;

    public function setup(): void
    {
        $this->firstProduct   = $this->createMock(ProductInterface::class);
        $this->secondProduct  = $this->createMock(ProductInterface::class);
        $this->customerOption = $this->createMock(CustomerOptionInterface::class);
        $someValue            = $this->createMock(CustomerOptionValueInterface::class);
        $otherValue           = $this->createMock(CustomerOptionValueInterface::class);
        $someChannel          = $this->createMock(ChannelInterface::class);
        $otherChannel         = $this->createMock(ChannelInterface::class);

        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $productRepository->method('findOneByCode')->willReturnMap([
            ['first_product', $this->firstProduct],
            ['second_product', $this->secondProduct],
        ]);

        $this->validator = $this->createMock(ValidatorInterface::class);

        $channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $channelRepository->method('findOneByCode')->willReturnMap([
            ['some_channel', $someChannel],
            ['other_channel', $otherChannel],
        ]);

        $customerOptionRepository                 = $this->createMock(CustomerOptionRepositoryInterface::class);
        $customerOptionValueRepository            = $this->createMock(CustomerOptionValueRepositoryInterface::class);
        $this->customerOptionValuePriceRepository = $this->createMock(CustomerOptionValuePriceRepositoryInterface::class);
        $this->customerOptionValuePriceFactory    = $this->createMock(CustomerOptionValuePriceFactoryInterface::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);


        $customerOptionRepository
            ->method('findOneByCode')->with('some_option')
            ->willReturn($this->customerOption)
        ;

        $customerOptionValueRepository
            ->method('findOneBy')
            ->willReturnCallback(function (array $array) use ($someValue, $otherValue) {
                if ($array['code'] === 'some_value' && $array['customerOption'] === $this->customerOption) {
                    return $someValue;
                }

                if ($array['code'] === 'other_value' && $array['customerOption'] === $this->customerOption) {
                    return $otherValue;
                }

                throw new Exception('Using this set of parameters'.var_export($array, true).' is not mocked');
            })
        ;

        $this->customerOptionPriceImporter = new CustomerOptionPriceImporter(
            $this->entityManager,
            $productRepository,
            $this->validator,
            $customerOptionRepository,
            $customerOptionValueRepository,
            $channelRepository,
            $this->customerOptionValuePriceRepository,
            $this->customerOptionValuePriceFactory
        );
    }

    public function testCreatingNewPrices(): void
    {
        $valuePrice = $this->createMock(CustomerOptionValuePriceInterface::class);

        $this->customerOptionValuePriceFactory
            ->expects(self::exactly(3))
            ->method('createNew')
            ->willReturn($valuePrice)
        ;

        $this->entityManager
            ->expects(self::exactly(3))
            ->method('persist')
            ->with(self::isInstanceOf(CustomerOptionValuePriceInterface::class))
        ;
        $this->entityManager->expects(self::once())->method('flush');

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList->expects(self::exactly(3))->method('count')->willReturn(0);
        $this->validator
            ->expects(self::exactly(3))
            ->method('validate')
            ->with(self::isInstanceOf(ProductInterface::class), null, 'sylius')
            ->willReturn($violationList)
        ;

        Assert::equalTo(
            new PriceImportResult(3, 0, []),
            $this->customerOptionPriceImporter->import($this->getCreateData())
        );
    }

    public function testUpdatingExistingPrices(): void
    {
        $this->customerOptionValuePriceFactory->expects(self::never())->method('createNew');

        $valuePrice = $this->createMock(CustomerOptionValuePriceInterface::class);

        $this->customerOptionValuePriceRepository
            ->expects(self::once())
            ->method('find')->with(10)
            ->willReturn($valuePrice)
        ;

        $this->entityManager
            ->expects(self::once())
            ->method('persist')->with(self::isInstanceOf(CustomerOptionValuePriceInterface::class))
        ;
        $this->entityManager->expects(self::once())->method('flush');

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with(self::isInstanceOf(ProductInterface::class), null, 'sylius')
            ->willReturn($violationList)
        ;
        $violationList->expects(self::once())->method('count')->willReturn(0);

        self::assertEquals(
            new PriceImportResult(1, 0, []),
            $this->customerOptionPriceImporter->import($this->getUpdateData())
        );
    }

    public function testDeleteExistingPrices(): void
    {
        $this->customerOptionValuePriceFactory->expects(self::never())->method('createNew');

        $valuePrice = $this->createMock(CustomerOptionValuePriceInterface::class);

        $this->customerOptionValuePriceRepository
            ->expects(self::once())
            ->method('find')->with(10)
            ->willReturn($valuePrice)
        ;

        $this->firstProduct
            ->expects(self::once())
            ->method('removeCustomerOptionValuePrice')
            ->with($valuePrice);

        $this->entityManager->expects(self::once())->method('persist')->with($this->firstProduct);
        $this->entityManager->expects(self::once())->method('flush');

        self::assertEquals(
            new PriceImportResult(1, 0, []),
            $this->customerOptionPriceImporter->import($this->getDeleteData())
        );
    }

    public function testReturningImportErrors(): void
    {
        $valuePrice = $this->createMock(CustomerOptionValuePriceInterface::class);

        $this->customerOptionValuePriceRepository->expects(self::never())->method('find');
        $this->customerOptionValuePriceFactory->expects(self::exactly(3))->method('createNew')->willReturn($valuePrice);

        $this->firstProduct->expects(self::exactly(2))
            ->method('addCustomerOptionValuePrice')->with($valuePrice)
        ;
        $this->secondProduct->expects(self::exactly(1))
            ->method('addCustomerOptionValuePrice')->with($valuePrice)
        ;

        $this->firstProduct->expects(self::exactly(2))
            ->method('removeCustomerOptionValuePrice')->with($valuePrice)
        ;
        $this->secondProduct->expects(self::exactly(1))
            ->method('removeCustomerOptionValuePrice')->with($valuePrice)
        ;

        $this->entityManager
            ->expects(self::never())
            ->method('persist')->with(self::isInstanceOf(CustomerOptionValuePriceInterface::class));
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $this->validator
            ->expects(self::exactly(3))
            ->method('validate')
            ->with(self::isInstanceOf(ProductInterface::class), null, 'sylius')
            ->willReturn($violationList)
        ;
        $violationList->expects(self::exactly(3))->method('count')->willReturn(1);

        $expected = new PriceImportResult(0, 3, [
            'first_product' => [
                [
                    'violations' => $violationList,
                    'data'       => $this->getCreateData()[0],
                    'message'    => '',
                ],
                [
                    'violations' => $violationList,
                    'data'       => $this->getCreateData()[2],
                    'message'    => '',
                ]
            ],
            'second_product' => [[
                'violations' => $violationList,
                'data'       => $this->getCreateData()[1],
                'message'    => '',
            ]],
        ]);

        self::assertEquals(
            $expected,
            $this->customerOptionPriceImporter->import($this->getCreateData())
        );
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
