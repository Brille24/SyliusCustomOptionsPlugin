<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Enumerations\CustomerOptionTypeEnum;
use Brille24\SyliusCustomerOptionsPlugin\Form\Product\ShopCustomerOptionType;
use Brille24\SyliusCustomerOptionsPlugin\Services\OrderItemOptionUpdaterInterface;
use DateTime;
use Exception;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

class EditCustomerOptionsAction
{
    public function __construct(
        private Environment $twig,
        private RouterInterface $router,
        private FormFactoryInterface $formFactory,
        private OrderItemRepositoryInterface $orderItemRepository,
        private OrderItemOptionUpdaterInterface $orderItemOptionUpdater,
        private EventDispatcherInterface $eventDispatcher,
        private bool $recalculatePrice,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(Request $request): Response
    {
        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->find($request->attributes->get('orderItem'));
        Assert::notNull($orderItem);

        $this->eventDispatcher->dispatch(
            new ResourceControllerEvent($orderItem),
            'brille24.order_item.pre_update',
        );

        /** @var OrderInterface $order */
        $order = $orderItem->getOrder();

        $orderItemForm = $this->formFactory->create(
            ShopCustomerOptionType::class,
            $this->getCustomerOptionValues($orderItem),
            ['product' => $orderItem->getProduct(), 'channel' => $order->getChannel(), 'mapped' => true],
        );

        $orderItemForm->handleRequest($request);

        if ($orderItemForm->isSubmitted() && $orderItemForm->isValid()) {
            $this->orderItemOptionUpdater->updateOrderItemOptions($orderItem, $orderItemForm->getData(), $this->recalculatePrice);

            $this->eventDispatcher->dispatch(
                new ResourceControllerEvent($orderItem),
                'brille24.order_item.post_update',
            );

            return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $order->getId()]));
        }

        return new Response(
            $this->twig->render('@Brille24SyliusCustomerOptionsPlugin/Order/editCustomerOption.html.twig', [
                'customerOptionForm' => $orderItemForm->createView(),
                'order' => $order,
            ]),
        );
    }

    /**
     * @throws Exception
     */
    private function getCustomerOptionValues(OrderItemInterface $orderItem): array
    {
        $optionAsAssociativeArray = [];
        $orderItemOptions = $orderItem->getCustomerOptionConfiguration();

        foreach ($orderItemOptions as $orderItemOption) {
            $customerOption = $orderItemOption->getCustomerOption();
            $customerOptionType = $customerOption->getType();
            $code = $orderItemOption->getCustomerOptionCode();

            // Select options use CustomerOptionValues
            if ($customerOptionType === CustomerOptionTypeEnum::MULTI_SELECT) {
                $optionAsAssociativeArray[$code][] = $orderItemOption->getCustomerOptionValue();
            } elseif ($customerOptionType === CustomerOptionTypeEnum::SELECT) {
                $optionAsAssociativeArray[$code] = $orderItemOption->getCustomerOptionValue();
            } else {
                $optionAsAssociativeArray[$code] = $this->transformValue($customerOptionType, $orderItemOption->getScalarValue());
            }
        }

        return $optionAsAssociativeArray;
    }

    /**
     * @return bool|DateTime|float|string|null
     *
     * @throws Exception
     */
    private function transformValue(string $customerOptionType, ?string $value)
    {
        if (null === $value) {
            return null;
        }

        return match ($customerOptionType) {
            CustomerOptionTypeEnum::BOOLEAN => (bool) $value,
            CustomerOptionTypeEnum::NUMBER => (float) $value,
            CustomerOptionTypeEnum::DATE, CustomerOptionTypeEnum::DATETIME => new DateTime($value),
            CustomerOptionTypeEnum::FILE => null,
            default => $value,
        };
    }
}
