<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Importer;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Brille24\SyliusCustomerOptionsPlugin\Exceptions\ConstraintViolationException;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Object\PriceImportResult;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class CustomerOptionPriceImporter implements CustomerOptionPriceImporterInterface
{
    protected const BATCH_SIZE = 100;

    protected EntityManagerInterface $entityManager;

    protected ProductRepositoryInterface $productRepository;

    /** @var array<ProductInterface|null> */
    protected array $products = [];

    protected ValidatorInterface $validator;

    protected CustomerOptionRepositoryInterface $customerOptionRepository;

    protected CustomerOptionValueRepositoryInterface $customerOptionValueRepository;

    protected ChannelRepositoryInterface $channelRepository;

    protected RepositoryInterface $customerOptionValuePriceRepository;

    protected CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepositoryInterface $productRepository,
        ValidatorInterface $validator,
        CustomerOptionRepositoryInterface $customerOptionRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->validator = $validator;
        $this->customerOptionRepository = $customerOptionRepository;
        $this->customerOptionValueRepository = $customerOptionValueRepository;
        $this->channelRepository = $channelRepository;
        $this->customerOptionValuePriceRepository = $customerOptionValuePriceRepository;
        $this->customerOptionValuePriceFactory = $customerOptionValuePriceFactory;
    }

    public function import(array $data): PriceImportResult
    {
        // Handle update
        $i = 0;
        $failed = 0;
        $errors = [];
        foreach ($data as $datum) {
            $productCode = $datum['product_code'];

            try {
                $this->importRow($datum, $productCode);

                if (++$i % self::BATCH_SIZE === 0) {
                    $this->entityManager->flush();
                }
            } catch (ConstraintViolationException $violationException) {
                ++$failed;
                $errors[$productCode][] = [
                    'violations' => $violationException->getViolations(),
                    'data' => $datum,
                    'message' => $violationException->getMessage(),
                ];
            } catch (\Throwable $exception) {
                ++$failed;
                $errors[$productCode][] = ['data' => $datum, 'message' => $exception->getMessage()];
            }
        }

        $this->entityManager->flush();

        return new PriceImportResult($i, $failed, $errors);
    }

    protected function importRow(array $datum, string $productCode): void
    {
        $id = $datum['id'];
        $validFrom = $datum['valid_from'];
        $validTo = $datum['valid_to'];
        $customerOptionCode = $datum['customer_option_code'];
        $customerOptionValueCode = $datum['customer_option_value_code'];
        $channelCode = $datum['channel_code'];
        $type = $datum['type'];
        $amount = (int) $datum['amount'];
        $percent = (float) $datum['percent'];
        $delete = filter_var($datum['delete'], \FILTER_VALIDATE_BOOL);

        $product = $this->getProduct($productCode);
        Assert::isInstanceOf(
            $product,
            ProductInterface::class,
            sprintf('Product with code "%s" not found', $productCode),
        );

        $price = null;
        if (null !== $id) {
            $price = $this->customerOptionValuePriceRepository->find($id);

            Assert::isInstanceOf(
                $price,
                CustomerOptionValuePriceInterface::class,
                sprintf('Value price with id "%s" not found', $id),
            );
        }

        // Handle deletion of prices
        if (null !== $price && $delete) {
            $product->removeCustomerOptionValuePrice($price);
            $this->entityManager->persist($product);

            return;
        }

        // Build the date range object
        $dateRange = null;
        if (null !== $validFrom && null !== $validTo) {
            $validFrom = new \DateTime($validFrom);
            $validTo = new \DateTime($validTo);
            $dateRange = new DateRange($validFrom, $validTo);
        }

        if (null === $price) {
            $price = $this->createNewPrice(
                $customerOptionCode,
                $customerOptionValueCode,
                $channelCode,
                $product,
            );
        }

        $price->setDateValid($dateRange);
        $price->setType($type);
        $price->setAmount($amount);
        $price->setPercent($percent);

        // Add the value price to the product so we can use it in the validation.
        $product->addCustomerOptionValuePrice($price);

        $violations = $this->validator->validate($product, null, 'sylius');
        if (count($violations) > 0) {
            $product->removeCustomerOptionValuePrice($price);

            throw new ConstraintViolationException($violations);
        }

        $this->entityManager->persist($price);
    }

    protected function getProduct(string $code): ?ProductInterface
    {
        if (!isset($this->products[$code])) {
            /** @var ProductInterface|null $product */
            $product = $this->productRepository->findOneByCode($code);
            $this->products[$code] = $product;
        }

        return $this->products[$code];
    }

    protected function createNewPrice(
        string $customerOptionCode,
        string $customerOptionValueCode,
        string $channelCode,
        ProductInterface $product,
    ): CustomerOptionValuePriceInterface {
        $customerOption = $this->customerOptionRepository->findOneByCode($customerOptionCode);

        /** @var CustomerOptionValueInterface|null $customerOptionValue */
        $customerOptionValue = $this->customerOptionValueRepository->findOneBy([
            'code' => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ]);

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::isInstanceOf(
            $customerOptionValue,
            CustomerOptionValueInterface::class,
            sprintf('CustomerOptionValue with code "%s" not found', $customerOptionValueCode),
        );
        Assert::isInstanceOf(
            $channel,
            ChannelInterface::class,
            sprintf('Channel with code "%s" not found', $channelCode),
        );

        // Create new price
        /** @var CustomerOptionValuePriceInterface $valuePrice */
        $valuePrice = $this->customerOptionValuePriceFactory->createNew();

        $valuePrice->setCustomerOptionValue($customerOptionValue);
        $valuePrice->setChannel($channel);
        $valuePrice->setProduct($product);

        return $valuePrice;
    }
}
