<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Services;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionValueInterface;

interface CustomerOptionValueResolverInterface
{
    public function resolve(CustomerOptionInterface $customerOption, string $value): ?CustomerOptionValueInterface;
}