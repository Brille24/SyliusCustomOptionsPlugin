<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Entity;

use Brille24\CustomerOptionsBundle\Entity\CustomerOptions\CustomerOptionInterface;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface, CustomerOptionInterface
{

}