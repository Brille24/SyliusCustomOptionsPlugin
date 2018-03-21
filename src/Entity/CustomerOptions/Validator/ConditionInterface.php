<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator;


use Brille24\CustomerOptionsPlugin\Traits\ConditionTraitInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ConditionInterface extends ConditionTraitInterface, ResourceInterface
{
}