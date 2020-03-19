<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\Tools;

use DateTimeInterface;
use InvalidArgumentException;

class DateRange implements DateRangeInterface
{
    /** @var int|null */
    private $id;

    /**
     * @var DateTimeInterface
     */
    private $start;

    /**
     * @var DateTimeInterface
     */
    private $end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function __construct(DateTimeInterface $start, DateTimeInterface $end)
    {
        if ($end < $start) {
            throw new InvalidArgumentException('End can\'t be greater than start');
        }

        $this->start = $start;
        $this->end   = $end;
    }

    /** {@inheritdoc} */
    public function contains(DateTimeInterface $current): bool
    {
        $afterStart = $this->start <= $current;
        $beforeEnd  = $this->end >= $current;

        return $afterStart && $beforeEnd;
    }

    /** {@inheritdoc} */
    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    /** {@inheritdoc} */
    public function getEnd(): ?DateTimeInterface
    {
        return $this->end;
    }

    /** {@inheritdoc} */
    public function equals(DateRangeInterface $other): bool
    {
        return $this->start->getTimestamp() === $other->getStart()->getTimestamp() &&
            $this->end->getTimestamp() === $other->getEnd()->getTimestamp();
    }
}
