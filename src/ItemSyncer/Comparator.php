<?php

namespace Gupalo\ItemSyncer;

use Gupalo\DateUtils\DateUtils;

class Comparator
{
    public static function equalsDay($a, $b): bool
    {
        if ($a === null && $b === null) {
            return true;
        }

        return DateUtils::isSameDay($a, $b);
    }

    public static function equalsTime($a, $b): bool
    {
        if ($a === null && $b === null) {
            return true;
        }

        return DateUtils::create($a)->getTimestamp() === DateUtils::create($b)->getTimestamp();
    }

    public static function equalsFloat($a, $b, float $allowedDelta = 0.000001): bool
    {
        return abs((float)$a - (float)$b) <= $allowedDelta;
    }
}
