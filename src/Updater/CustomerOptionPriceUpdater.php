<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Updater;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValuePriceInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRangeInterface;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CustomerOptionValuePriceFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionValueRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Validator\Constraints\CustomerOptionValuePriceDateOverlapConstraint;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

class CustomerOptionPriceUpdater implements CustomerOptionPriceUpdaterInterface
{
    /** @var CustomerOptionRepositoryInterface */
    protected $customerOptionRepository;

    /** @var RepositoryInterface */
    protected $customerOptionValuePriceRepository;

    /** @var CustomerOptionValueRepositoryInterface */
    protected $customerOptionValueRepository;

    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var CustomerOptionValuePriceFactoryInterface */
    protected $customerOptionValuePriceFactory;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(
        CustomerOptionRepositoryInterface $customerOptionRepository,
        RepositoryInterface $customerOptionValuePriceRepository,
        CustomerOptionValueRepositoryInterface $customerOptionValueRepository,
        ChannelRepositoryInterface $channelRepository,
        CustomerOptionValuePriceFactoryInterface $customerOptionValuePriceFactory,
        ValidatorInterface $validator
    ) {
        $this->customerOptionRepository           = $customerOptionRepository;
        $this->customerOptionValuePriceRepository = $customerOptionValuePriceRepository;
        $this->customerOptionValueRepository      = $customerOptionValueRepository;
        $this->channelRepository                  = $channelRepository;
        $this->customerOptionValuePriceFactory    = $customerOptionValuePriceFactory;
        $this->validator                          = $validator;
    }

    /** {@inheritdoc} */
    public function updateForProduct(
        string $customerOptionCode,
        string $customerOptionValueCode,
        string $channelCode,
        ProductInterface $product,
        ?string $validFrom,
        ?string $validTo,
        string $type,
        int $amount,
        float $percent
    ): CustomerOptionValuePriceInterface {
        $dateRange = null;
        if (null !== $validFrom && null !== $validTo) {
            $validFrom = new \DateTime($validFrom);
            $validTo   = new \DateTime($validTo);
            $dateRange = new DateRange($validFrom, $validTo);
        }

        $price = $this->getPrice($customerOptionCode, $customerOptionValueCode, $channelCode, $product, $dateRange);

        $price->setDateValid($dateRange);
        $price->setType($type);
        $price->setAmount($amount);
        $price->setPercent($percent);

        // Validate the prices
        $prices  = clone $product->getCustomerOptionValuePrices();
        $prices->add($price);

        $constraint = new CustomerOptionValuePriceDateOverlapConstraint();
        $violations = $this->validator->validate($prices, $constraint);

        Assert::count($violations, 0, $constraint->message);

        return $price;
    }

    /**
     * @param string $customerOptionCode
     * @param string $customerOptionValueCode
     * @param string $channelCode
     * @param ProductInterface|null $product
     * @param DateRangeInterface|null $dateRange
     *
     * @return CustomerOptionValuePriceInterface
     */
    private function getPrice(
        string $customerOptionCode,
        string $customerOptionValueCode,
        string $channelCode,
        ?ProductInterface $product,
        ?DateRangeInterface $dateRange
    ): CustomerOptionValuePriceInterface {
        $customerOption = $this->customerOptionRepository->findOneByCode($customerOptionCode);

        /** @var CustomerOptionValueInterface|null $customerOptionValue */
        $customerOptionValue = $this->customerOptionValueRepository->findOneBy([
            'code'           => $customerOptionValueCode,
            'customerOption' => $customerOption,
        ]);

        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::isInstanceOf(
            $customerOptionValue,
            CustomerOptionValueInterface::class,
            sprintf('CustomerOptionValue with code "%s" not found', $customerOptionValueCode)
        );
        Assert::isInstanceOf(
            $channel,
            ChannelInterface::class,
            sprintf('Channel with code "%s" not found', $channelCode)
        );

        // Try to find an existing price
        /** @var CustomerOptionValuePriceInterface[] $prices */
        $prices = $this->customerOptionValuePriceRepository->findBy([
            'customerOptionValue' => $customerOptionValue,
            'channel'             => $channel,
            'product'             => $product,
        ]);

        $valuePrice = null;
        foreach ($prices as $price) {
            $dateValid = $price->getDateValid();

            if ($dateRange === $dateValid) {
                $valuePrice = $price;
            }

            if (null !== $dateValid && null !== $dateRange) {
                if ($dateValid->compare($dateRange)) {
                    $valuePrice = $price;
                }
            }
        }

        // If no price exists, create a new one
        if (null === $valuePrice) {
            // Create new price
            /** @var CustomerOptionValuePriceInterface $valuePrice */
            $valuePrice = $this->customerOptionValuePriceFactory->createNew();

            $valuePrice->setCustomerOptionValue($customerOptionValue);
            $valuePrice->setChannel($channel);

            if (null !== $product) {
                $valuePrice->setProduct($product);
            }
        }

        return $valuePrice;
    }
}
