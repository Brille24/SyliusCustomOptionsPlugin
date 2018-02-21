<?php

declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Traits\CustomerOptionableTraitInterface;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface, CustomerOptionableTraitInterface
{
}
