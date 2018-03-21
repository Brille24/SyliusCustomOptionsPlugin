<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions\Validator;


use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class ErrorMessage implements ErrorMessageInterface
{
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
    }

    /** @var int */
    private $id;

    /** @var ValidatorInterface */
    private $validator;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    protected function createTranslation(): TranslationInterface
    {
        return new ErrorMessageTranslation();
    }

    public function getValidator(): ?ValidatorInterface
    {
        return $this->validator;
    }

    public function setValidator(?ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }
}