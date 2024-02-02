<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\CustomerOptionGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'brille24_customer_option_group_validator')]
class Validator implements ValidatorInterface
{
    public const DEFAULT_ERROR_MESSAGE = 'This combination of values is not valid.';

    /** @var int */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /** @var Collection */
    #[ORM\OneToMany(targetEntity: ConditionInterface::class, mappedBy: 'validator', orphanRemoval: true, cascade: ['all'])]
    protected $conditions;

    /** @var Collection */
    #[ORM\OneToMany(targetEntity: ConstraintInterface::class, mappedBy: 'validator', orphanRemoval: true, cascade: ['all'])]
    protected $constraints;

    /** @var CustomerOptionGroupInterface|null */
    #[ORM\ManyToOne(targetEntity: CustomerOptionGroupInterface::class, inversedBy: 'validators')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected $customerOptionGroup;

    /** @var ErrorMessageInterface */
    #[ORM\OneToOne(targetEntity: ErrorMessage::class, cascade: ['all'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected $errorMessage;

    public function __construct()
    {
        $this->constraints = new ArrayCollection();
        $this->conditions = new ArrayCollection();

        $this->errorMessage = self::createDefaultErrorMessage();
    }

    /** @inheritdoc */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** @inheritdoc */
    public function getConditions(): array
    {
        return $this->conditions->toArray();
    }

    /** @inheritdoc */
    public function setConditions(?array $conditions): void
    {
        $this->conditions->clear();
        if ($conditions === null) {
            return;
        }

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }
    }

    /** @inheritdoc */
    public function addCondition(ConditionInterface $condition): void
    {
        $condition->setValidator($this);

        $this->conditions->add($condition);
    }

    /** @inheritdoc */
    public function removeCondition(ConditionInterface $condition): void
    {
        $condition->setValidator(null);

        $this->conditions->removeElement($condition);
    }

    /** @inheritdoc */
    public function getConstraints(): array
    {
        return $this->constraints->toArray();
    }

    /** @inheritdoc */
    public function setConstraints(?array $constraints): void
    {
        $this->constraints->clear();

        if ($constraints === null) {
            return;
        }

        foreach ($constraints as $constraint) {
            $this->addConstraint($constraint);
        }
    }

    /** @inheritdoc */
    public function addConstraint(ConstraintInterface $constraint): void
    {
        $constraint->setValidator($this);

        $this->constraints->add($constraint);
    }

    /** @inheritdoc */
    public function removeConstraint(ConstraintInterface $constraint): void
    {
        $constraint->setValidator(null);

        $this->constraints->removeElement($constraint);
    }

    /** @inheritdoc */
    public function getCustomerOptionGroup(): ?CustomerOptionGroupInterface
    {
        return $this->customerOptionGroup;
    }

    /** @inheritdoc */
    public function setCustomerOptionGroup(?CustomerOptionGroupInterface $customerOptionGroup): void
    {
        $this->customerOptionGroup = $customerOptionGroup;
    }

    public function getErrorMessage(): ?ErrorMessageInterface
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?ErrorMessageInterface $message): void
    {
        $this->errorMessage = $message ?? self::createDefaultErrorMessage();

        $this->errorMessage->setValidator($this);
    }

    private static function createDefaultErrorMessage(): ErrorMessageInterface
    {
        $errorMessage = new ErrorMessage();
        $errorMessage->setCurrentLocale('en_US');
        $errorMessage->setMessage(self::DEFAULT_ERROR_MESSAGE);

        return $errorMessage;
    }
}
