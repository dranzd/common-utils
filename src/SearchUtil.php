<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use InvalidArgumentException;

/**
 * Builds SQL LIKE patterns from user-typed search text.
 *
 * The result is a value to bind as a query parameter, never to concatenate into SQL. Escaping only
 * works if the database uses the same escape character, so either rely on the engine's default or
 * name the character in the query:
 *
 * | Engine           | With the default backslash                    | With another character |
 * |------------------|-----------------------------------------------|------------------------|
 * | MySQL, MariaDB   | default; no clause (explicit form: `ESCAPE '\\'`) | `ESCAPE '!'`           |
 * | PostgreSQL       | default; no clause needed                     | `ESCAPE '!'`           |
 * | SQLite, Oracle   | no default; needs `ESCAPE '\'`                | `ESCAPE '!'`           |
 *
 * Passing a non-backslash escape character and writing `ESCAPE '!'` reads the same on every
 * engine and is the portable form. SQL Server also treats `[` as a wildcard, which this class does
 * not escape, so SQL Server is not supported.
 *
 * Input is trimmed and split on Unicode whitespace; control characters are kept as they are.
 */
final class SearchUtil
{
    /**
     * Escape the escape character, `%` and `_` so they match literally in a LIKE pattern.
     *
     * @throws InvalidArgumentException If the value is not valid UTF-8 or the escape character is invalid
     *                                  (see toLoose()).
     */
    public static function escape(string $value, string $escapeChar = '\\'): string
    {
        self::assertEscapeChar($escapeChar);

        if (!mb_check_encoding($value, 'UTF-8')) {
            throw new InvalidArgumentException('Value must be valid UTF-8.');
        }

        return str_replace(
            [$escapeChar, '%', '_'],
            [$escapeChar . $escapeChar, $escapeChar . '%', $escapeChar . '_'],
            $value
        );
    }

    /**
     * Turn whitespace-separated terms into a pattern: terms escaped and joined with `%`.
     *
     * Returns an empty string for an empty or whitespace-only filter, which as a LIKE pattern
     * matches only the empty string; the toLooseInclude*() wrappers return `%` instead.
     *
     * The escape character must be exactly one valid UTF-8 character that is not `%`, `_`,
     * whitespace or a control character.
     *
     * @throws InvalidArgumentException If the filter is not valid UTF-8 or the escape character is invalid.
     */
    public static function toLoose(string $filter, string $escapeChar = '\\'): string
    {
        self::assertEscapeChar($escapeChar);

        $terms = preg_split('/\s+/u', $filter, -1, PREG_SPLIT_NO_EMPTY);
        if ($terms === false) {
            throw new InvalidArgumentException('Filter must be valid UTF-8.');
        }

        return implode('%', array_map(
            static fn (string $term): string => self::escape($term, $escapeChar),
            $terms
        ));
    }

    /**
     * Pattern that matches values containing the terms: `%…%`.
     *
     * An empty filter gives a single `%` (match all).
     *
     * @throws InvalidArgumentException See toLoose().
     */
    public static function toLooseIncludeEnds(string $filter, string $escapeChar = '\\'): string
    {
        return self::wrap(self::toLoose($filter, $escapeChar), '%', '%');
    }

    /**
     * Pattern that matches values ending with the terms: `%…`.
     *
     * An empty filter gives a single `%` (match all).
     *
     * @throws InvalidArgumentException See toLoose().
     */
    public static function toLooseIncludeLeft(string $filter, string $escapeChar = '\\'): string
    {
        return self::wrap(self::toLoose($filter, $escapeChar), '%', '');
    }

    /**
     * Pattern that matches values starting with the terms: `…%`.
     *
     * An empty filter gives a single `%` (match all).
     *
     * @throws InvalidArgumentException See toLoose().
     */
    public static function toLooseIncludeRight(string $filter, string $escapeChar = '\\'): string
    {
        return self::wrap(self::toLoose($filter, $escapeChar), '', '%');
    }

    private static function assertEscapeChar(string $escapeChar): void
    {
        if (
            !mb_check_encoding($escapeChar, 'UTF-8')
            || mb_strlen($escapeChar) !== 1
            || $escapeChar === '%'
            || $escapeChar === '_'
            || preg_match('/^[\s\p{Z}\p{Cc}]$/u', $escapeChar) === 1
        ) {
            throw new InvalidArgumentException(
                'Escape character must be one valid UTF-8 character that is not "%", "_", whitespace '
                . 'or a control character.'
            );
        }
    }

    private static function wrap(string $pattern, string $left, string $right): string
    {
        return $pattern === '' ? '%' : $left . $pattern . $right;
    }
}
