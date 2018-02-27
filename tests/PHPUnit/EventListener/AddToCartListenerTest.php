<?php
declare(strict_types=1);

namespace Test\Brille24\CustomerOptionsPlugin\EventListener;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOption;
use Brille24\CustomerOptionsPlugin\Event\AddToCartListener;
use Brille24\CustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Brille24\CustomerOptionsPlugin\Repository\CustomerOptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\{
    ParameterBag, Request, RequestStack
};

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

    public function __construct()
    {
        parent::__construct();
        $this->channel = self::createMock(ChannelInterface::class);
    }

    public function setUp()
    {
        // Aliasing variables for use in testing
//        $request           = &$this->request;
        $customerOptions   = &$this->customerOptions;
        $entitiesPersisted = &$this->entitiesPersisted;

        $requestStack = self::createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturnCallback(function () {
            return $this->request;
        });

        $entityManager = self::createMock(EntityManagerInterface::class);
        $entityManager->method('persist')->willReturnCallback(function ($entity) use (&$entitiesPersisted) {
            $entitiesPersisted[] = $entity;
        });

        $orderItemOptionFactory = self::createMock(OrderItemOptionFactoryInterface::class);
        $orderItemOptionFactory->method('createNewFromStrings')->willReturnCallback(function ($customerOptionCode, $value){
            if(!array_key_exists($customerOptionCode, $this->customerOptions)){
                throw new \Exception('Not found');
            }

            return new OrderItemOption($this->channel, $this->customerOptions[$customerOptionCode], $value);
        });

        $this->addToCartListener = new AddToCartListener(
            $requestStack,
            $entityManager,
            $orderItemOptionFactory
        );
    }

    private function createEvent(bool $hasOrder): ResourceControllerEvent
    {
        $order = self::createMock(OrderInterface::class);

        $orderItem = self::createMock(OrderItemInterface::class);
        $orderItem->method('getOrder')->willReturn($hasOrder ? $order : null);

        $event = self::createMock(ResourceControllerEvent::class);
        $event->method('getSubject')->willReturn($orderItem);

        return $event;
    }

    private function createRequest(array $customerOptions): Request
    {
        $request = new Request();

        $request->request = self::createMock(ParameterBag::class);
        $request->request->method('get')->willReturnCallback(function ($key) use ($customerOptions) {
            self::assertEquals('sylius_add_to_cart', $key);
            return $customerOptions;
        });

        return $request;
    }

    public function testWithIncreasedProduct(): void
    {
        # SETUP
        $event = $this->createEvent(false);

        # EXECUTE
        $this->addToCartListener->addItemToCart($event);

        # ASSERT
        self::assertEquals(0, count($this->entitiesPersisted));
    }

    public function testWithEmptyCustomerOptions(): void
    {
        # SETUP
        $event         = $this->createEvent(true);
        $this->request = $this->createRequest([]);

        # EXECUTE
        $this->addToCartListener->addItemToCart($event);

        # ASSERT
        self::assertEquals(1, count($this->entitiesPersisted));
    }

    public function testInvalidCustomerOptionCode(): void
    {
        # SETUP
        $event         = $this->createEvent(true);
        $this->request = $this->createRequest(['customerOptions' => ['customerOptionCode' => 'value']]);

        self::expectException(\Exception::class);
        self::expectExceptionMessage('Not found');

        # EXECUTE
        $this->addToCartListener->addItemToCart($event);
    }

    public function testValidCustomerOptionCode(): void
    {
        # SETUP
        $this->customerOptions['customerOptionCode'] = self::createMock(CustomerOptionInterface::class);

        $event         = $this->createEvent(true);
        $this->request = $this->createRequest(['customerOptions' => ['customerOptionCode' => 'value']]);

        # EXECUTE
        $this->addToCartListener->addItemToCart($event);

        # ASSERT
        self::assertEquals(2, count($this->entitiesPersisted));
    }
}

