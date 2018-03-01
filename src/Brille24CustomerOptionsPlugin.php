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

namespace Brille24\CustomerOptionsPlugin;

use Brille24\CustomerOptionsPlugin\DependencyInjection\Brille24CustomerOptionsExtension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class Brille24CustomerOptionsPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getContainerExtension()
    {
        $class = $this->getContainerExtensionClass();

        return new $class();
    }

    public function getContainerExtensionClass(): string
    {
        return Brille24CustomerOptionsExtension::class;
    }
}
