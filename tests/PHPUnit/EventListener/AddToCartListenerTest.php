<?php

declare(strict_types=1);

namespace Test\Brille24\SyliusCustomerOptionsPlugin\EventListener;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\SyliusCustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\SyliusCustomerOptionsPlugin\EventListener\AddToCartListener;
use Brille24\SyliusCustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\SyliusCustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AddToCartListenerTest extends TestCase
{
    /** @var AddToCartListener */
    private $addToCartListener;

    /** @var Request */
    private $request;

    /** @var CustomerOptionInterface[] */
    private $customerOptions = [];

    /** @var array */
    private $entitiesPersisted = [];

    /** @var ChannelInterface */
    private $channel;

    /** @var CustomerOptionRepositoryInterface */
    private $customerOptionRepository;

    public function __construct()
    {
        parent::__construct();
        $this->channel = $this->createMock(ChannelInterface::class);
    }

    //<editor-fold desc="Setup">
    public function setUp(): void
    {
        // Aliasing variables for use in testing
        $entitiesPersisted = &$this->entitiesPersisted;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturnCallback(function () {
            return $this->request;
        });

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('persist')->willReturnCallback(function ($entity) use (&$entitiesPersisted) {
            if (!array_key_exists(get_class($entity), $entitiesPersisted)) {
                $entitiesPersisted[get_class($entity)] = 1;
            }
            ++$entitiesPersisted[get_class($entity)];
        });

        $orderItemOptionFactory = $this->createMock(OrderItemOptionFactoryInterface::class);
        $orderItemOptionFactory->method('createNewFromStrings')->willReturnCallback(
            function ($orderItem, $customerOptionCode, $value) {
                if (!array_key_exists($customerOptionCode, $this->customerOptions)) {
                    throw new \Exception('Not found');
                }

                $orderItemOption = new OrderItemOption(
                    $this->channel,
                    $this->customerOptions[$customerOptionCode],
                    $value
                );
                $orderItemOption->setOrderItem($orderItem);

                return $orderItemOption;
            }
        );

        $orderProcessor = $this->createMock(OrderProcessorInterface::class);

        $this->customerOptionRepository = $this->createMock(CustomerOptionRepositoryInterface::class);

        $this->addToCartListener = new AddToCartListener(
            $requestStack,
            $entityManager,
            $orderItemOptionFactory,
            $orderProcessor,
            $this->customerOptionRepository
        );
    }

    private function createEvent(bool $hasOrder): ResourceControllerEvent
    {
        $product = $this->createConfiguredMock(ProductInterface::class, []);

        $channel = $this->createMock(ChannelInterface::class);
        $order = $this->createConfiguredMock(OrderInterface::class, ['getChannel' => $channel]);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $orderItem->method('getOrder')->willReturn($hasOrder ? $order : null);
        $orderItem->method('getProduct')->willReturn($product);

        $event = $this->createMock(ResourceControllerEvent::class);
        $event->method('getSubject')->willReturn($orderItem);

        return $event;
    }

    private function createRequest(array $customerOptions): Request
    {
        $request = new Request();

        $request->request = $this->createMock(ParameterBag::class);
        $request->request->method('get')->willReturnCallback(function ($key) use ($customerOptions) {
            self::assertEquals('sylius_add_to_cart', $key);

            return $customerOptions;
        });

        return $request;
    }

    //</editor-fold>

    public function testWithIncreasedProduct(): void
    {
        // SETUP
        $event = $this->createEvent(false);

        // EXECUTE
        $this->addToCartListener->addItemToCart($event);

        // ASSERT
        self::assertCount(0, $this->entitiesPersisted);
    }

    public function testWithEmptyCustomerOptions(): void
    {
        // SETUP
        $event = $this->createEvent(true);
        $this->request = $this->createRequest([]);

        // EXECUTE
        $this->addToCartListener->addItemToCart($event);

        // ASSERT
        self::assertCount(1, $this->entitiesPersisted);
    }

    public function testInvalidCustomerOptionCode(): void
    {
        // SETUP
        $this->customerOptionRepository->method('findOneByCode')->with('customerOptionCode')->willReturn(
            $this->createMock(CustomerOptionInterface::class)
        );
        $event = $this->createEvent(true);
        $this->request = $this->createRequest(['customer_options' => ['customerOptionCode' => 'value']]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not found');

        // EXECUTE
        $this->addToCartListener->addItemToCart($event);
    }

    public function testValidCustomerOptionCode(): void
    {
        // SETUP
        $this->customerOptions['customerOptionCode'] = $this->createMock(CustomerOptionInterface::class);
        $this->customerOptionRepository->method('findOneByCode')->with('customerOptionCode')->willReturn(
            $this->customerOptions['customerOptionCode']
        );

        $event = $this->createEvent(true);
        $this->request = $this->createRequest(['customer_options' => ['customerOptionCode' => 'value']]);

        // EXECUTE
        $this->addToCartListener->addItemToCart($event);

        // ASSERT
        self::assertCount(2, $this->entitiesPersisted);
    }
}
