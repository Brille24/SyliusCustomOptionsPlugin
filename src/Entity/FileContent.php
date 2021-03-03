<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class FileContent implements ResourceInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $content;

    public function __construct($content)
    {
        $this->content = (string) $content;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}
