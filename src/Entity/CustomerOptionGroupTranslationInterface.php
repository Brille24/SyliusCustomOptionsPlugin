<?php
declare(strict_types=1);

namespace Brille24\CustomerOptionsBundle\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CustomerOptionGroupTranslationInterface extends ResourceInterface
{
    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return null|string
     */
    public function getName(): ?string;
}