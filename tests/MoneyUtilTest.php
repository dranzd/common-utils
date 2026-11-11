<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use Dranzd\Common\Utils\MoneyUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoneyUtil::class)]
final class MoneyUtilTest extends TestCase
{
    /**
     * @return iterable<string, array{float, int, int}>
     */
    public static function toMinorUnits(): iterable
    {
        yield 'classic float trap' => [19.99, 100, 1999];
        yield 'sum of floats' => [0.1 + 0.2, 100, 30];
        yield 'hundredths with float noise' => [1.15, 100, 115];
        yield 'trailing zero' => [8.20, 100, 820];
        yield 'negative' => [-19.99, 100, -1999];
        yield 'half away from zero positive' => [0.125, 100, 13];
        yield 'half away from zero negative' => [-0.125, 100, -13];
        yield 'zero' => [0.0, 100, 0];
        yield 'negative zero' => [-0.0, 100, 0];
        yield 'jpy' => [1234.0, 1, 1234];
        yield 'kwd' => [1.234, 1000, 1234];
        yield 'fractional major unit' => [2.5, 100, 250];
        yield 'largest safe amount' => [562949953421312.0, 1, 562949953421312];
        yield 'largest safe negative amount' => [-562949953421312.0, 1, -562949953421312];
        yield 'large two-decimal amount' => [5629499534213.12, 100, 562949953421312];
    }

    #[DataProvider('toMinorUnits')]
    public function testFromFloatToLowestDenomination(float $value, int $denomination, int $expected): void
    {
        self::assertSame($expected, MoneyUtil::fromFloatToLowestDenomination($value, $denomination));
    }

    public function testFromFloatUsesDefaultDenomination(): void
    {
        self::assertSame(1999, MoneyUtil::fromFloatToLowestDenomination(19.99));
    }

    /**
     * @return iterable<string, array{float, int, string}>
     */
    public static function invalidFloatInputs(): iterable
    {
        yield 'nan' => [NAN, 100, 'finite'];
        yield 'infinity' => [INF, 100, 'finite'];
        yield 'negative infinity' => [-INF, 100, 'finite'];
        yield 'one past the safe bound' => [562949953421313.0, 1, 'out of range'];
        yield 'one past the negative safe bound' => [-562949953421313.0, 1, 'out of range'];
        yield 'amount that used to be silently off by one' => [10000126640353.45, 100, 'out of range'];
        yield 'beyond int range' => [9.3e18, 1, 'out of range'];
        yield 'exactly 2^63' => [9223372036854775808.0, 1, 'out of range'];
        yield 'overflow caused by denomination' => [1.0e17, 100, 'out of range'];
        yield 'safe value pushed out by denomination' => [562949953421.313, 1000, 'out of range'];
        yield 'multiplication overflows to infinity' => [1.0e308, 1000, 'out of range'];
        yield 'zero denomination' => [1.0, 0, 'at least 1'];
        yield 'negative denomination' => [1.0, -100, 'at least 1'];
    }

    #[DataProvider('invalidFloatInputs')]
    public function testFromFloatRejectsInvalidInput(float $value, int $denomination, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        MoneyUtil::fromFloatToLowestDenomination($value, $denomination);
    }

    /**
     * @return iterable<string, array{int, int, float}>
     */
    public static function toMajorUnits(): iterable
    {
        yield 'usd' => [1999, 100, 19.99];
        yield 'jpy' => [500, 1, 500.0];
        yield 'kwd' => [1234, 1000, 1.234];
        yield 'negative' => [-1999, 100, -19.99];
        yield 'zero' => [0, 100, 0.0];
    }

    #[DataProvider('toMajorUnits')]
    public function testFromLowestDenominationToFloat(int $value, int $denomination, float $expected): void
    {
        self::assertSame($expected, MoneyUtil::fromLowestDenominationToFloat($value, $denomination));
    }

    public function testFromLowestDenominationToFloatUsesDefaultDenomination(): void
    {
        self::assertSame(0.01, MoneyUtil::fromLowestDenominationToFloat(1));
    }

    public function testFromLowestDenominationToFloatRejectsDenominationBelowOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        MoneyUtil::fromLowestDenominationToFloat(100, 0);
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function roundTrips(): iterable
    {
        yield 'zero' => [0, 100];
        yield 'one cent' => [1, 100];
        yield 'negative cents' => [-1999, 100];
        yield 'jpy' => [123456, 1];
        yield 'kwd' => [-1234567, 1000];
        yield 'prime-ish cents' => [7_654_321_987, 100];
        yield 'just below a quadrillion cents' => [562949953421311, 100];
        yield 'safe bound in cents' => [MoneyUtil::MAX_SAFE_MINOR_UNITS, 100];
        yield 'negative safe bound in cents' => [-MoneyUtil::MAX_SAFE_MINOR_UNITS, 100];
        yield 'safe bound in mills' => [MoneyUtil::MAX_SAFE_MINOR_UNITS, 1000];
        yield 'safe bound in whole units' => [MoneyUtil::MAX_SAFE_MINOR_UNITS, 1];
    }

    #[DataProvider('roundTrips')]
    public function testRoundTripOfIntegerAmountsIsLossless(int $amount, int $denomination): void
    {
        $float = MoneyUtil::fromLowestDenominationToFloat($amount, $denomination);

        self::assertSame($amount, MoneyUtil::fromFloatToLowestDenomination($float, $denomination));
    }

    public function testRoundTripOfSampledCentAmountsIsLossless(): void
    {
        for ($cents = -100000; $cents <= 100000; $cents += 997) {
            $float = MoneyUtil::fromLowestDenominationToFloat($cents);

            self::assertSame($cents, MoneyUtil::fromFloatToLowestDenomination($float));
        }
    }
}
