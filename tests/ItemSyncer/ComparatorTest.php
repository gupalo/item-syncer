<?php

namespace Gupalo\ItemSyncer\Tests;

use Gupalo\ItemSyncer\Comparator;
use PHPUnit\Framework\TestCase;

class ComparatorTest extends TestCase
{

    public function testEqualsDat(): void
    {
        self::assertTrue(Comparator::equalsDay(null, null));
        self::assertTrue(Comparator::equalsDay('2001-01-01', '2001-01-01 00:00:00'));
        self::assertTrue(Comparator::equalsDay('2001-01-01 00:10', '2001-01-01 00:10:00'));
        self::assertTrue(Comparator::equalsDay('2001-01-01 00:10', '2001-01-01 00:10:00'));
        self::assertTrue(Comparator::equalsDay('2001-01-01 00:10:20', '2001-01-01 00:10:20'));
        self::assertTrue(Comparator::equalsDay(new \DateTimeImmutable('2001-01-01 00:10:20'), new \DateTime('2001-01-01 00:10:20')));
        self::assertTrue(Comparator::equalsDay('2001-01-01 00:10:30', '2001-01-01 12:10:00'));

        self::assertFalse(Comparator::equalsDay('2001-01-01', '2001-01-02'));
        self::assertFalse(Comparator::equalsDay('2001-01-01 00:10:30', '2001-01-02 12:10:00'));
    }

    public function testEqualsTime(): void
    {
        self::assertTrue(Comparator::equalsTime(null, null));
        self::assertTrue(Comparator::equalsTime('2001-01-01', '2001-01-01 00:00:00'));
        self::assertTrue(Comparator::equalsTime('2001-01-01 00:10', '2001-01-01 00:10:00'));
        self::assertTrue(Comparator::equalsTime('2001-01-01 00:10', '2001-01-01 00:10:00'));
        self::assertTrue(Comparator::equalsTime('2001-01-01 00:10:20', '2001-01-01 00:10:20'));
        self::assertTrue(Comparator::equalsTime(new \DateTimeImmutable('2001-01-01 00:10:20'), new \DateTime('2001-01-01 00:10:20')));

        self::assertFalse(Comparator::equalsTime('2001-01-01 00:10:30', '2001-01-01 00:10:20'));
    }

    public function testEqualsFloat(): void
    {
        self::assertTrue(Comparator::equalsFloat(0, null));
        self::assertTrue(Comparator::equalsFloat(1, 1));
        self::assertTrue(Comparator::equalsFloat(1, 1.0));
        self::assertTrue(Comparator::equalsFloat(1, 0.9999999999999999999999999));
        self::assertTrue(Comparator::equalsFloat(1.0000000000000000001, 0.9999999999999));
        self::assertTrue(Comparator::equalsFloat(7, 8, allowedDelta: 1));

        self::assertFalse(Comparator::equalsFloat(1, 2));
        self::assertFalse(Comparator::equalsFloat(1, 1.01));
    }
}
