<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use InvalidArgumentException;

/**
 * Case conversion between snake_case, camelCase, PascalCase and kebab-case.
 *
 * All conversions share one tokenizer, so any input style can be converted to any output style.
 * Words are split at runs of characters that are neither letters, digits nor combining marks, and at
 * case boundaries (`firstName`, `HTTPServer`). Digits stay attached to the preceding word, so
 * `userID2Name` is split into `user`, `id2` and `name`. Matching is Unicode-aware and the input must
 * be valid UTF-8.
 *
 * Limits of the tokenizer:
 * - Separators are dropped wherever they occur, so `_id`, `__typename` and `$ref` lose their prefix.
 * - Acronyms are normalised: `userID` becomes `userId` and `iOS` becomes `iOs`.
 * - A lower-case plural after an acronym splits oddly: `MyURLs` becomes `my_ur_ls`.
 * - A zero-width joiner is a separator, so it splits words.
 * - Repeated conversion is stable except for single-letter words and for first letters whose title case
 *   has several characters or is not an upper-case letter (`ß` gives `Ss`, `ǆ` gives `ǅ`): PascalCase and
 *   camelCase then merge words that snake_case would keep apart.
 */
final class StringUtil
{
    private const WORD_BOUNDARY = '/[^\p{L}\p{N}\p{M}]+'
        . '|(?<=[\p{Ll}\p{N}\p{M}])(?=\p{Lu})'
        . '|(?<=\p{Lu})(?=\p{Lu}\p{Ll})/u';

    /**
     * Convert a string to snake_case.
     *
     * @throws InvalidArgumentException If the string is not valid UTF-8.
     */
    public static function toSnakeCase(string $string): string
    {
        return implode('_', self::words($string));
    }

    /**
     * Convert a string to camelCase.
     *
     * @throws InvalidArgumentException If the string is not valid UTF-8.
     */
    public static function toCamelCase(string $string): string
    {
        $words = self::words($string);
        $first = array_shift($words) ?? '';

        return $first . self::capitalize($words);
    }

    /**
     * Convert a string to PascalCase.
     *
     * @throws InvalidArgumentException If the string is not valid UTF-8.
     */
    public static function toPascalCase(string $string): string
    {
        return self::capitalize(self::words($string));
    }

    /**
     * Convert a string to kebab-case.
     *
     * @throws InvalidArgumentException If the string is not valid UTF-8.
     */
    public static function toKebabCase(string $string): string
    {
        return implode('-', self::words($string));
    }

    /**
     * Title-case the first letter of each word and join them.
     *
     * Title case, unlike upper case, maps `ǆ` to `ǅ` rather than `Ǆ` and `ß` to `Ss` rather than `SS`.
     *
     * @param list<string> $words
     */
    private static function capitalize(array $words): string
    {
        return implode('', array_map(
            static fn (string $word): string => mb_convert_case(mb_substr($word, 0, 1), MB_CASE_TITLE)
                . mb_substr($word, 1),
            $words,
        ));
    }

    /**
     * Split a string into lower-cased words.
     *
     * @return list<string>
     *
     * @throws InvalidArgumentException If the string is not valid UTF-8.
     */
    private static function words(string $string): array
    {
        $parts = preg_split(self::WORD_BOUNDARY, $string, -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === false) {
            throw new InvalidArgumentException('Cannot split string into words: ' . preg_last_error_msg());
        }

        return array_map(mb_strtolower(...), $parts);
    }
}
