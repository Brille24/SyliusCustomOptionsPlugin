<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsPlugin\Entity\CustomerOptions;

use Sylius\Component\Resource\Model\AbstractTranslation;

class CustomerOptionGroupTranslation extends AbstractTranslation implements CustomerOptionGroupTranslationInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

}