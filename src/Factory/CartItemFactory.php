<?php

/**
 * This file is part of the Brille24 customer options plugin.
 *
 * (c) Brille24 GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

class CartItemFactory implements CartItemFactoryInterface
{
    private CartItemFactoryInterface $decoratedFactory;

    public function __construct(
        FactoryInterface $decoratedFactory,
        private ProductVariantResolverInterface $variantResolver,
        private RequestStack $requestStack,
        private OrderItemOptionFactoryInterface $orderItemOptionFactory,
        private CustomerOptionRepositoryInterface $customerOptionRepository,
    ) {
        $this->decoratedFactory = new \Sylius\Component\Core\Factory\CartItemFactory($decoratedFactory, $variantResolver);
    }

    public function createForProduct(ProductInterface $product): OrderItemInterface
    {
        return $this->decoratedFactory->createForProduct($product);
    }

    public function createForCart(OrderInterface $order): OrderItemInterface
    {
        return $this->decoratedFactory->createForCart($order);
    }

    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    public function createForProductWithCustomerOption(ProductInterface $product): OrderItemInterface
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->createNew();
        $cartItem->setVariant($this->variantResolver->getVariant($product));

        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return $cartItem;
        }

        $customerOptionConfiguration = $this->getCustomerOptionsFromRequest($request);

        $salesOrderConfigurations = [];
        foreach ($customerOptionConfiguration as $customerOptionCode => $valueArray) {
            if (!is_array($valueArray)) {
                $valueArray = [$valueArray];
            }

            foreach ($valueArray as $key => $value) {
                if (is_array($value)) {
                    $valueArray = array_merge($valueArray, $value);
                    unset($valueArray[$key]);
                }
            }

            foreach ($valueArray as $value) {
                // Creating the item
                $salesOrderConfiguration = $this->orderItemOptionFactory->createNewFromStrings(
                    $cartItem,
                    $customerOptionCode,
                    $value,
                );

                $salesOrderConfigurations[] = $salesOrderConfiguration;
            }
        }

        $cartItem->setCustomerOptionConfiguration($salesOrderConfigurations);

        return $cartItem;
    }

    /**
     * Gets the customer options from the request
     */
    private function getCustomerOptionsFromRequest(Request $request): array
    {
        /** @var array $addToCart */
        $addToCart = $request->request->all('sylius_add_to_cart');

        if (!isset($addToCart['customer_options'])) {
            return [];
        }

        // Date options need a little extra attention
        // We transform the date fields into a single date string
        foreach ($addToCart['customer_options'] as $code => $value) {
            $customerOption = $this->customerOptionRepository->findOneByCode($code);
            Assert::notNull($customerOption);

            switch ($customerOption->getType()) {
                case CustomerOptionTypeEnum::DATE:
                    $day = $value['day'];
                    $month = $value['month'];
                    $year = $value['year'];
                    $addToCart['customer_options'][$code] = sprintf('%d-%d-%d', $year, $month, $day);

                    break;
                case CustomerOptionTypeEnum::DATETIME:
                    $date = $value['date'];
                    $time = $value['time'];
                    $day = $date['day'];
                    $month = $date['month'];
                    $year = $date['year'];

                    $hour = $time['hour'] ?? 0;
                    $minute = $time['minute'] ?? 0;

                    $addToCart['customer_options'][$code] = sprintf('%d-%d-%d %d:%d', $year, $month, $day, $hour, $minute);

                    break;
            }
        }

        return $addToCart['customer_options'];
    }
}
