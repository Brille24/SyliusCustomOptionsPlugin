<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;


use Brille24\CustomerOptionsPlugin\Traits\ConditionTrait;

class Condition implements ConditionInterface
{
    use ConditionTrait;

    /** @var int */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}