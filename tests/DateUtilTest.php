<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use Dranzd\Common\Utils\DateUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateUtil::class)]
final class DateUtilTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function dates(): iterable
    {
        yield 'leap day' => ['2024-02-29', 'Y-m-d', true];
        yield 'non-leap feb 29' => ['2023-02-29', 'Y-m-d', false];
        yield 'feb 30' => ['2024-02-30', 'Y-m-d', false];
        yield 'month 13' => ['2024-13-01', 'Y-m-d', false];
        yield 'relative word' => ['tomorrow', 'Y-m-d', false];
        yield 'empty string' => ['', 'Y-m-d', false];
        yield 'before 1901' => ['1800-01-01', 'Y-m-d', true];
        yield 'non-padded with padded format' => ['2024-1-5', 'Y-m-d', false];
        yield 'trailing data' => ['2024-01-05 10:00', 'Y-m-d', false];
        yield 'custom format' => ['31/12/2024', 'd/m/Y', true];
        yield 'hour 25' => ['2024-06-01 25:00:00', 'Y-m-d H:i:s', false];
        yield 'atom with offset' => ['2024-06-01T10:00:00+02:00', DATE_ATOM, true];
        yield 'non-padded format accepts non-padded' => ['5/1/2024', 'j/n/Y', true];
        yield 'non-padded format rejects padded day' => ['05/1/2024', 'j/n/Y', false];
        yield 'day name matches date' => ['Mon, 01 Jan 2024', 'D, d M Y', true];
        yield 'day name does not match date' => ['Tue, 01 Jan 2024', 'D, d M Y', false];
        yield 'month name wrong case' => ['01 jan 2024', 'd M Y', false];
        yield 'padded month with non-padded format' => ['01', 'n', false];
        yield 'non-padded month with non-padded format' => ['1', 'n', true];
        yield 'atom with Z suffix is rejected' => ['2024-06-01T10:00:00Z', DATE_ATOM, false];
        yield 'atom with zero offset' => ['2024-06-01T10:00:00+00:00', DATE_ATOM, true];
        yield 'p accepts Z' => ['2024-06-01T10:00:00Z', 'Y-m-d\TH:i:sp', true];
        yield 'p rejects zero offset spelled out' => ['2024-06-01T10:00:00+00:00', 'Y-m-d\TH:i:sp', false];
        yield 'timezone identifier' => ['2024-03-05 Europe/Paris', 'Y-m-d e', true];
        yield 'unknown timezone identifier' => ['2024-03-05 Foo', 'Y-m-d e', false];
        yield 'timezone abbreviation' => ['2024-03-05 EST', 'Y-m-d T', true];
        yield 'unknown timezone abbreviation' => ['2024-03-05 XYZ', 'Y-m-d T', false];
        yield 'offset beyond 24 hours is not checked' => ['2024-03-05 +25:00', 'Y-m-d e', true];
        yield 'unix timestamp' => ['1709647629', 'U', true];
        yield 'escaped literal' => ['2024 y', 'Y \y', true];
        yield 'escaped unusable character is a literal' => ['2024 N', 'Y \N', true];
        yield 'escaped backslash then literal' => ['2024\\', 'Y\\\\', true];
        yield 'nul byte in date' => ["2024-02-29\0", 'Y-m-d', false];
        yield 'nul byte alone' => ["\0", 'Y-m-d', false];
        // Fields missing from the format come from the epoch (1970-01-01), not from today. The next three
        // fail on some calendar days only if the "!" reset prefix is dropped (31 days, non-leap February).
        yield 'day 31 exists in the epoch month' => ['31', 'd', true];
        yield 'feb 29 does not exist in the epoch year' => ['02-29', 'm-d', false];
        yield 'feb 28 exists in the epoch year' => ['02-28', 'm-d', true];
        yield 'time only' => ['10:00', 'H:i', true];
    }

    #[DataProvider('dates')]
    public function testIsDateValid(string $date, string $format, bool $expected): void
    {
        self::assertSame($expected, DateUtil::isDateValid($date, $format));
    }

    public function testDefaultFormatIsIsoDate(): void
    {
        self::assertTrue(DateUtil::isDateValid('2024-02-29'));
        self::assertFalse(DateUtil::isDateValid('29/02/2024'));
    }

    public function testResultDoesNotDependOnDefaultTimezone(): void
    {
        $original = date_default_timezone_get();
        date_default_timezone_set('America/New_York');

        try {
            self::assertTrue(DateUtil::isDateValid('2024-03-10 02:30', 'Y-m-d H:i'));
        } finally {
            date_default_timezone_set($original);
        }
    }

    public function testEmptyFormatThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Date format must not be empty.');

        DateUtil::isDateValid('2024-01-01', '');
    }

    #[TestWith(["Y-m-d\0"])]
    #[TestWith(["\0"])]
    public function testNulByteInFormatThrows(string $format): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('NUL byte');

        DateUtil::isDateValid('2024-01-01', $format);
    }

    public function testLoneTrailingBackslashThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('lone backslash');

        DateUtil::isDateValid('2024', 'Y\\');
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function unusableFormats(): iterable
    {
        yield 'N' => ['N', 'it cannot be parsed'];
        yield 'w' => ['w', 'it cannot be parsed'];
        yield 'W' => ['W', 'it cannot be parsed'];
        yield 'o' => ['o', 'it cannot be parsed'];
        yield 't' => ['t', 'it cannot be parsed'];
        yield 'L' => ['L', 'it cannot be parsed'];
        yield 'B' => ['B', 'it cannot be parsed'];
        yield 'I' => ['I', 'it cannot be parsed'];
        yield 'Z' => ['Z', 'it cannot be parsed'];
        yield 'c suggests DATE_ATOM' => ['c', 'it cannot be parsed; use DATE_ATOM instead'];
        yield 'r suggests DATE_RFC2822' => ['r', 'it cannot be parsed; use DATE_RFC2822 instead'];
        yield 'question mark' => ['?', 'it matches input that cannot be formatted back'];
        yield 'asterisk' => ['*', 'it matches input that cannot be formatted back'];
        yield 'plus' => ['+', 'it matches input that cannot be formatted back'];
        yield 'exclamation mark' => ['!', 'it resets fields and cannot be formatted back'];
        yield 'pipe' => ['|', 'it resets fields and cannot be formatted back'];
    }

    #[DataProvider('unusableFormats')]
    public function testUnusableFormatCharacterThrows(string $character, string $reason): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('"%s" can never validate: %s', $character, $reason));

        DateUtil::isDateValid('2024-01-01', 'Y-m-d' . $character);
    }

    public function testUnusableFormatCharacterIsFoundAfterAnEscape(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('"N"');

        DateUtil::isDateValid('2024-01-01', 'Y\\NN');
    }
}
