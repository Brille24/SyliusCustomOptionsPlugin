<?php

declare(strict_types=1);

namespace Tests\Brille24\SyliusCustomerOptionsPlugin\PHPUnit\Entity\Tools;

use Brille24\SyliusCustomerOptionsPlugin\Entity\Tools\DateRange;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function testConstructFail(): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('End can\'t be smaller than start');

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
        $date1 = new DateTime('2010-01-01 12:00:01');
        $date2 = new DateTime('2010-11-01 12:00:00');

        return [
            'dateRange length 1 contains'     => [$date, $date1, $date, true],
            'dateRange length 1 not contains' => [$date, $date1, $date2, false],
            'dateRange'                       => [$date, $date2, new DateTime('2010-05-01'), true],
            'dateRange not contains'          => [$date, $date2, new DateTime('2011-05-01'), false],
        ];
    }

    /** @dataProvider dataOverlaps */
    public function testOverlaps(DateRange $dateRange1, DateRange $dateRange2, bool $overlapping): void
    {
        self::assertSame($overlapping, $dateRange1->overlaps($dateRange2));
    }

    public function dataOverlaps(): array
    {
        $firstDate = new DateTimeImmutable('2010-01-01 12:00:00');
        $secondDate = new DateTimeImmutable('2011-01-01 12:00:00');
        $thridDate = new DateTimeImmutable('2012-08-01 12:00:00');
        $fourthDate = new DateTimeImmutable('2013-01-01 12:00:00');

        return [
            'overlaps with to determined ranges' => [
                new DateRange($firstDate, $thridDate),
                new DateRange($secondDate, $fourthDate),
                true
            ],
            'does not overlap with to determined ranges' => [
                new DateRange($firstDate, $secondDate),
                new DateRange($thridDate, $fourthDate),
                false
            ],
            'inside also overlaps' => [
                new DateRange($firstDate, $secondDate),
                new DateRange(
                    $firstDate->add(new DateInterval('P1D')), // One day after the first date
                    $secondDate->sub(new DateInterval('P1D')) // One day before the second date
                ),
                true
            ],
        ];
    }
}
