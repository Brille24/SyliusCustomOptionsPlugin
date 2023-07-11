<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests/Behat',
        __DIR__ . '/tests/PHPUnit',
    ]);

    $ecsConfig->import('vendor/sylius-labs/coding-standard/ecs.php');
};
