<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\Tools;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'brille24_customer_option_date_range')]
class DateRange implements DateRangeInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $start;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $end;

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
            throw new InvalidArgumentException('End can\'t be smaller than start');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /** @inheritdoc */
    public function contains(DateTimeInterface $current): bool
    {
        $afterStart = $this->start <= $current;
        $beforeEnd = $this->end > $current;

        return $afterStart && $beforeEnd;
    }

    public function overlaps(DateRangeInterface $other): bool
    {
        if ($other->getEnd() < $this->start || $this->end < $other->getStart()) {
            return false;
        }

        return true;
    }

    /** @inheritdoc */
    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    /** @inheritdoc */
    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }

    /** @inheritdoc */
    public function equals(DateRangeInterface $other): bool
    {
        return $this->start->getTimestamp() === $other->getStart()->getTimestamp() &&
            $this->end->getTimestamp() === $other->getEnd()->getTimestamp();
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->start->format(\DATE_RFC1123), $this->end->format(\DATE_RFC1123));
    }
}
