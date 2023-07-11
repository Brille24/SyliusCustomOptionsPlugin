<?php

declare(strict_types=1);

namespace Brille24\SyliusCustomerOptionsPlugin\Entity\Tools;

use DateTimeInterface;

interface DateRangeInterface
{
    /**
     * Checks if a DateRange contains a date
     */
    public function contains(DateTimeInterface $current): bool;

    public function getStart(): DateTimeInterface;

    public function getEnd(): DateTimeInterface;

    public function equals(self $other): bool;
}
