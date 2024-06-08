<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Traits\ConditionTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'brille24_customer_option_group_validator_condition')]
class Condition implements ConditionInterface
{
    use ConditionTrait;

    #[ORM\ManyToOne(targetEntity: ValidatorInterface::class, cascade: ['persist'], inversedBy: 'conditions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected ?ValidatorInterface $validator = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
