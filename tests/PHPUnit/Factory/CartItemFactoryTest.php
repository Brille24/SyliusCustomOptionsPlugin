<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Factory;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\SyliusCustomerOptionsPlugin\Factory\CartItemFactory;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Traits\OrderItemCustomerOptionCapableTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CartItemFactoryTest extends TestCase
{
    private CartItemFactory $cartItemFactory;
    /** @var MockObject|RequestStack */
    private $requestStack;
    /**
     * @var MockObject|OrderItemInterface
     */
    private $orderItem;
    /**
     * @var MockObject|OrderItemOptionFactoryInterface
     */
    private $orderItemOptionFactory;
    /**
     * @var MockObject|CustomerOptionRepositoryInterface
     */
    private $customerOptionRepository;

    protected function setUp(): void
    {
        $this->orderItem = new class extends OrderItem implements OrderItemInterface {
            use OrderItemCustomerOptionCapableTrait;
        };

        $decoratedFactory = $this->createMock(CartItemFactoryInterface::class);
        $decoratedFactory->method('createNew')->willReturn($this->orderItem);

        $variantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->orderItemOptionFactory = $this->createMock(OrderItemOptionFactoryInterface::class);
        $this->customerOptionRepository = $this->createMock(CustomerOptionRepositoryInterface::class);

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->cartItemFactory = new CartItemFactory(
            $decoratedFactory,
            $variantResolver,
            $this->requestStack,
            $this->orderItemOptionFactory,
            $this->customerOptionRepository
        );
    }

    /**
     * @test
     */
    public function createForProductWithCustomerOptionWithoutRequest(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn(null);

        $product = $this->createMock(ProductInterface::class);
        $cartItem = $this->cartItemFactory->createForProductWithCustomerOption($product);

        $this->assertInstanceOf(OrderItemInterface::class, $cartItem);
        $this->assertCount(0, $cartItem->getCustomerOptionConfiguration());
    }

    /**
     * @test
     */
    public function createForProductWithCustomerOptionFromRequestWithoutCustomerOptions(): void
    {
        $request = new Request();
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        $product = $this->createMock(ProductInterface::class);
        $cartItem = $this->cartItemFactory->createForProductWithCustomerOption($product);

        $this->assertInstanceOf(OrderItemInterface::class, $cartItem);
        $this->assertCount(0, $cartItem->getCustomerOptionConfiguration());
    }

    /**
     * @test
     */
    public function createForProductWithCustomerOptionFromRequestWithCustomerOptions(): void
    {
        $customerOption = new CustomerOption();
        $customerOption->setCode('name');
        $customerOption->setCurrentLocale('en');

        $request = new Request([], ['sylius_add_to_cart' => ['customer_options' => ['name' => 'My name']]]);
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        $this->customerOptionRepository->method('findOneByCode')->with('name')->willReturn($customerOption);

        $this->orderItemOptionFactory->method('createNewFromStrings')->willReturnCallback(
            function ($orderItem, $customerOptionCode, $value) use ($customerOption) {
                $orderItemOption = new OrderItemOption();
                $orderItemOption->setOrderItem($orderItem);
                $orderItemOption->setCustomerOption($customerOption);
                $orderItemOption->setCustomerOptionValue($value);

                return $orderItemOption;
            },
        );

        $product = $this->createMock(ProductInterface::class);
        $cartItem = $this->cartItemFactory->createForProductWithCustomerOption($product);

        $this->assertInstanceOf(OrderItemInterface::class, $cartItem);
        $this->assertCount(1, $cartItem->getCustomerOptionConfiguration());
    }
}
