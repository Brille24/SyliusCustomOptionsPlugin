<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\CustomerOptions\Validator;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TranslatableInterface;

#[ORM\Entity]
#[ORM\Table(name: 'brille24_validator_error_message_translation')]
class ErrorMessageTranslation extends AbstractTranslation implements ErrorMessageTranslationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $message = null;

    #[ORM\ManyToOne(targetEntity: ErrorMessageInterface::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected ?TranslatableInterface $translatable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
