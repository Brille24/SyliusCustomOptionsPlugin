<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\Tools;

use DateTimeInterface;

interface DateRangeInterface
{
    /**
     * Checks if a DateRange contains a date
     *
     * @param DateTimeInterface $current
     *
     * @return bool
     */
    public function contains(DateTimeInterface $current): bool;

    /**
     * @return DateTimeInterface|null
     */
    public function getStart(): ?DateTimeInterface;

    /**
     * @return DateTimeInterface|null
     */
    public function getEnd(): ?DateTimeInterface;

    /**
     * @param DateRangeInterface $other
     *
     * @return bool
     */
    public function compare(DateRangeInterface $other): bool;
}
