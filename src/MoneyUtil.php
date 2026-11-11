<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use InvalidArgumentException;

/**
 * Converts between decimal major-unit amounts and integer minor-unit amounts.
 *
 * This is a boundary helper, for example for parsing user or API input. Money should be stored
 * and calculated as integer minor units, never as floats. The denomination is the number of minor
 * units per major unit: 100 for USD, 1 for JPY, 1000 for KWD.
 */
final class MoneyUtil
{
    /**
     * Largest absolute amount, in minor units, that converts exactly (2 ** 49, about 5.6e14).
     *
     * A double carries 53 bits, and the scaling multiplication and PHP 8.3's round() pre-rounding
     * (15 significant digits) both cost precision. Verified by randomised comparison against exact
     * integer arithmetic on decimal strings: no mismatch up to this bound for denominations 1 to
     * 10000, while mismatches begin near 1e15.
     */
    public const MAX_SAFE_MINOR_UNITS = 562949953421312;

    /**
     * Convert a major-unit amount to integer minor units, rounding half away from zero.
     *
     * The input is a binary float, so a decimal such as 1.005 is not represented exactly: the
     * result for such values depends on the float, and differs between PHP 8.3 and 8.4+.
     *
     * @throws InvalidArgumentException If the denomination is below 1, the value is not finite,
     *                                   or the amount exceeds MAX_SAFE_MINOR_UNITS.
     */
    public static function fromFloatToLowestDenomination(float $floatValue, int $lowestDenomination = 100): int
    {
        self::assertDenomination($lowestDenomination);

        if (!is_finite($floatValue)) {
            throw new InvalidArgumentException('Money value must be a finite number.');
        }

        $scaled = $floatValue * $lowestDenomination;
        if (abs($scaled) > (float) self::MAX_SAFE_MINOR_UNITS) {
            throw new InvalidArgumentException(sprintf(
                'Money value is out of range: more than %d minor units cannot be converted exactly.',
                self::MAX_SAFE_MINOR_UNITS
            ));
        }

        return (int) round($scaled);
    }

    /**
     * Convert integer minor units to a major-unit float.
     *
     * Converting back with fromFloatToLowestDenomination() returns the same integer whenever
     * the amount is within +/- MAX_SAFE_MINOR_UNITS.
     *
     * @throws InvalidArgumentException If the denomination is below 1.
     */
    public static function fromLowestDenominationToFloat(int $integerValue, int $lowestDenomination = 100): float
    {
        self::assertDenomination($lowestDenomination);

        return $integerValue / $lowestDenomination;
    }

    private static function assertDenomination(int $lowestDenomination): void
    {
        if ($lowestDenomination < 1) {
            throw new InvalidArgumentException('Lowest denomination must be at least 1.');
        }
    }
}
