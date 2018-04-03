<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Traits\ConditionTraitInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ConditionInterface extends ConditionTraitInterface, ResourceInterface
{
}
