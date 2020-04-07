<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ImportErrorHandlerPass implements CompilerPassInterface
{
    const SERVICE_ID = 'brille24.sylius_customer_options_plugin.handler.composite_import_error_handler';
    const TAG_NAME   = 'brille24.sylius_customer_options_plugin.import_error_handler';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::SERVICE_ID)) {
            return;
        }

        $importErrorHandler = $container->getDefinition(self::SERVICE_ID);

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged import error handlers need to have `type` attribute.');
            }

            $importErrorHandler->addMethodCall('addErrorHandler', [new Reference($id), $attributes[0]['type']]);
        }

        $container->setDefinition(self::SERVICE_ID, $importErrorHandler);
    }
}
