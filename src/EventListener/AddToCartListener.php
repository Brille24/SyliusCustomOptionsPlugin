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

namespace Brille24\CustomerOptionsPlugin\EventListener;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\ConditionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\ValidatorInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemInterface;
use Brille24\CustomerOptionsPlugin\Entity\OrderItemOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\ProductInterface;
use Brille24\CustomerOptionsPlugin\Factory\OrderItemOptionFactoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

final class AddToCartListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /** @var OrderItemOptionFactoryInterface */
    private $orderItemOptionFactory;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        OrderItemOptionFactoryInterface $itemOptionFactory
    )
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->orderItemOptionFactory = $itemOptionFactory;
    }

    public function addItemToCart(ResourceControllerEvent $event): void
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $event->getSubject();

        // If the order is null, it's an old order item with an existing reference in the database
        if ($orderItem->getOrder() === null) {
            return;
        }

        $customerOptionConfiguration = $this->getCustomerOptionsFromRequest($this->requestStack->getCurrentRequest());

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
                    $customerOptionCode,
                    $value
                );

                $salesOrderConfiguration->setOrderItem($orderItem);

                $this->entityManager->persist($salesOrderConfiguration);

                $salesOrderConfigurations[] = $salesOrderConfiguration;
            }
        }

        $orderItem->setCustomerOptionConfiguration($salesOrderConfigurations);
        $orderItem->recalculateUnitsTotal();

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

    }

    /**
     * Gets the customer options from the request
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCustomerOptionsFromRequest(Request $request): array
    {
        $addToCart = $request->request->get('sylius_add_to_cart');

        if (!isset($addToCart['customer_options'])) {
            return [];
        }

        return $addToCart['customer_options'];
    }


}
