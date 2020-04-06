<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ImportErrorHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('brille24.sylius_customer_options_plugin.handler.composite_import_error_handler')) {
            return;
        }

        $importErrorHandler = $container->getDefinition('brille24.sylius_customer_options_plugin.handler.composite_import_error_handler');

        foreach ($container->findTaggedServiceIds('brille24.sylius_customer_options_plugin.import_error_handler') as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged import error handlers need to have `type` attribute.');
            }

            $importErrorHandler->addMethodCall('addErrorHandler', [new Reference($id), $attributes['type']]);
        }
    }
}
