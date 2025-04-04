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

        // If the argument is a string then it's the name of the parameter where the services are configured
        // We then just need to modify the parameter.
        if (is_string($listOfAdjustmentsToClear)) {
            $this->addClearerToContainerParameter($container, $listOfAdjustmentsToClear);

            return;
        }

        // BC: For Symfony < 6 (modifing the service directly)
        $listOfAdjustmentsToClear[] = CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT;

        // Setting the new list as the new definition
        $clearerDefinition->setArgument(0, $listOfAdjustmentsToClear);
    }

    private function addClearerToContainerParameter(ContainerBuilder $container, string $parameterName): void
    {
        assert(1 === preg_match('/^%(.*)%$/', $parameterName, $matches));
        $parameterName = $matches[1];
        $listOfAdjustmentsToClear = $container->getParameter($parameterName);
        $listOfAdjustmentsToClear[] = CustomerOptionRecalculator::CUSTOMER_OPTION_ADJUSTMENT;

        $container->setParameter($parameterName, $listOfAdjustmentsToClear);
    }
}
