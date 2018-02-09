<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity;

use Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface, CustomerOptionInterface
{

}