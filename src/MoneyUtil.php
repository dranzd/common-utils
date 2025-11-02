<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

/**
 * Money utility functions for currency conversion.
 *
 * Provides framework-agnostic money manipulation utilities using native PHP.
 */
final class MoneyUtil
{
    /**
     * Convert a float money value to integer with lowest denomination
     *
     * Note: This also fixes the issue with float rounding error
     *
     * @param  float   $floatValue         Money in float value
     * @param  integer $lowestDenomination Lowest denomination of money
     * @return integer                     Value in the lowest denomination
     */
    public static function fromFloatToLowestDenomination(float $floatValue, int $lowestDenomination = 100): int
    {
        return (int) round($floatValue * $lowestDenomination);
    }

    /**
     * Convert the lowest denomination value to float value
     *
     * @param  integer $integerValue       Money in lowest denomination value
     * @param  integer $lowestDenomination Lowest denomination of money
     * @return float                       Value in the float value
     */
    public static function fromLowestDenominationToFloat(int $integerValue, int $lowestDenomination = 100): float
    {
        return (float) ($integerValue / $lowestDenomination);
    }
}
