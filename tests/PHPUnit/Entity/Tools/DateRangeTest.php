<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Entity\Tools;

use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function testConstructFail(): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('End can\'t be greater than start');

        $start = new DateTime('2010-11-01 12:00:00');
        $end   = new DateTime('2010-01-01 12:00:00');

        new DateRange($start, $end);
    }

    /** @dataProvider dataContains */
    public function testContains(
        DateTimeInterface $start,
        DateTimeInterface $end,
        DateTimeInterface $current,
        bool $contains
    ) {
        $dateRange = new DateRange($start, $end);

        self::assertEquals($contains, $dateRange->contains($current));
    }

    public function dataContains(): array
    {
        $date  = new DateTime('2010-01-01 12:00:00');
        $date1 = new DateTime('2010-11-01 12:00:00');

        return [
            'dateRange length 1 contains'     => [$date, $date, $date, true],
            'dateRange length 1 not contains' => [$date, $date, $date1, false],
            'dateRange'                       => [$date, $date1, new DateTime('2010-05-01'), true],
            'dateRange not contains'          => [$date, $date1, new DateTime('2011-05-01'), false],
        ];
    }
}
