<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\CompilerPass;

use Brille24\SyliusCustomerOptionsPlugin\Services\CustomerOptionRecalculator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddingTypesToAdjustmentClearerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Gets the definition of the OrderAdjustmentClearer
        $clearerDefinition = $container->getDefinition('sylius.order_processing.order_adjustments_clearer');

        // Getting the new list of adjustment types to clear
        $listOfAdjustmentsToClear = $clearerDefinition->getArgument(0);
        if (1 === preg_match('/^%(.*)%$/', $listOfAdjustmentsToClear, $matches)) {
            $listOfAdjustmentsToClear = $container->getParameter($matches[1]);
        }
        $listOfAdjustmentsToClear[] = CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT;

        // Setting the new list as the new definition
        $clearerDefinition->setArgument(0, $listOfAdjustmentsToClear);
    }
}
