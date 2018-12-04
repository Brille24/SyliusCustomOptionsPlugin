<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Controller;

use Brille24\SyliusCustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\SyliusCustomerOptionsPlugin\Form\Product\ShopCustomerOptionType;
use Brille24\SyliusCustomerOptionsPlugin\Services\OrderItemOptionUpdaterInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class EditCustomerOptionsAction extends Controller
{
    /** @var OrderItemRepositoryInterface */
    private $orderItemRepository;

    /** @var OrderItemOptionUpdaterInterface */
    private $orderItemOptionUpdater;

    public function __construct(
        OrderItemRepositoryInterface $orderItemRepository,
        OrderItemOptionUpdaterInterface $orderItemOptionUpdater
    ) {
        $this->orderItemRepository    = $orderItemRepository;
        $this->orderItemOptionUpdater = $orderItemOptionUpdater;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->find($request->attributes->get('orderItem'));
        Assert::notNull($orderItem);

        $order = $orderItem->getOrder();

        $orderItemForm = $this->createForm(
            ShopCustomerOptionType::class,
            $this->getCustomerOptionValues($orderItem),
            ['product' => $orderItem->getProduct(), 'mapped' => true]
        );

        $orderItemForm->handleRequest($request);

        if ($orderItemForm->isSubmitted() && $orderItemForm->isValid()) {
            $this->orderItemOptionUpdater->updateOrderItemOptions($orderItem, $orderItemForm->getData());
            $orderItem->recalculateUnitsTotal();

            return $this->redirectToRoute('sylius_admin_order_show', ['id' => $order->getId()]);
        }

        return $this->render(
            'Brille24SyliusCustomerOptionsPlugin:Order:editCustomerOption.html.twig',
            [
                'customerOptionForm' => $orderItemForm->createView(),
                'order'              => $order,
            ]
        );
    }

    private function getCustomerOptionValues(OrderItemInterface $orderItem): array
    {
        $optionAsAssociativeArray = [];
        $orderItemOptions         = $orderItem->getCustomerOptionConfiguration();

        foreach ($orderItemOptions as $orderItemOption) {
            $code  = $orderItemOption->getCustomerOptionCode();
            $value = $orderItemOption->getScalarValue();

            $optionAsAssociativeArray[$code] = $value;
        }

        return $optionAsAssociativeArray;
    }
}
