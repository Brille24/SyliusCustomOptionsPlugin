<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle;

use Brille24\CustomerOptionsBundle\DependencyInjection\Brille24CustomerOptionsExtension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class Brille24CustomerOptionsBundle extends Bundle
{
    use SyliusPluginTrait;

    public function getContainerExtension()
    {
        $class = $this->getContainerExtensionClass();
        return new $class();
    }

    public function getContainerExtensionClass()
    {
        return Brille24CustomerOptionsExtension::class;
    }
}
