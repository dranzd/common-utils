<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Strict, format-based date validation.
 */
final class DateUtil
{
    /**
     * Format characters that can never validate, with the reason shown in the exception.
     *
     * Either DateTimeImmutable::format() supports them but createFromFormat() cannot parse them, or
     * they are parser-only and format() writes them back as literals, so the round trip fails.
     */
    private const UNUSABLE_FORMAT_CHARACTERS = [
        'N' => 'it cannot be parsed',
        'w' => 'it cannot be parsed',
        'W' => 'it cannot be parsed',
        'o' => 'it cannot be parsed',
        't' => 'it cannot be parsed',
        'L' => 'it cannot be parsed',
        'B' => 'it cannot be parsed',
        'I' => 'it cannot be parsed',
        'Z' => 'it cannot be parsed',
        'c' => 'it cannot be parsed; use DATE_ATOM instead',
        'r' => 'it cannot be parsed; use DATE_RFC2822 instead',
        '?' => 'it matches input that cannot be formatted back',
        '*' => 'it matches input that cannot be formatted back',
        '+' => 'it matches input that cannot be formatted back',
        '!' => 'it resets fields and cannot be formatted back',
        '|' => 'it resets fields and cannot be formatted back',
    ];

    /**
     * Check that a string is a real date/time written exactly in the given format.
     *
     * The string must parse and format back to itself, so overflow such as `2024-02-30` and
     * non-padded input such as `2024-1-5` (for `Y-m-d`) are rejected. Fields the format does not
     * mention are taken from the Unix epoch (1970-01-01 00:00:00 UTC), and parsing is done in UTC,
     * so the result never depends on the clock or the default timezone.
     *
     * Only formats that can reproduce their input are usable. Characters that cannot (see the
     * exception below) and case-different month/day names never validate.
     *
     * @throws InvalidArgumentException If the format is empty, contains a NUL byte or an unescaped
     *                                  character that can never validate, or ends with a lone backslash.
     */
    public static function isDateValid(string $date, string $format = 'Y-m-d'): bool
    {
        self::assertFormat($format);

        if (str_contains($date, "\0")) {
            return false;
        }

        $parsed = DateTimeImmutable::createFromFormat('!' . $format, $date, new DateTimeZone('UTC'));
        if ($parsed === false) {
            return false;
        }

        return $parsed->format($format) === $date;
    }

    private static function assertFormat(string $format): void
    {
        if ($format === '') {
            throw new InvalidArgumentException('Date format must not be empty.');
        }

        if (str_contains($format, "\0")) {
            throw new InvalidArgumentException('Date format must not contain a NUL byte.');
        }

        $length = strlen($format);
        for ($i = 0; $i < $length; ++$i) {
            $character = $format[$i];

            if ($character === '\\') {
                if ($i === $length - 1) {
                    throw new InvalidArgumentException('Date format must not end with a lone backslash.');
                }

                ++$i;
                continue;
            }

            if (isset(self::UNUSABLE_FORMAT_CHARACTERS[$character])) {
                throw new InvalidArgumentException(sprintf(
                    'Date format character "%s" can never validate: %s.',
                    $character,
                    self::UNUSABLE_FORMAT_CHARACTERS[$character]
                ));
            }
        }
    }
}
