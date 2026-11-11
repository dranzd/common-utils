<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use Closure;
use InvalidArgumentException;

/**
 * Array key case conversion and recursive merging.
 */
final class ArrayUtil
{
    /**
     * Convert all string keys to snake_case.
     *
     * Integer keys are left untouched, nested arrays are converted only when `$recursive` is true and
     * other values (including objects) are returned as they are. See {@see self::convertKeys()} for the
     * collision rules shared by all `keyTo*Case` methods.
     *
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     *
     * @throws InvalidArgumentException If two keys convert to the same key, or a key is not valid UTF-8.
     */
    public static function keyToSnakeCase(array $array, bool $recursive = false): array
    {
        return self::convertKeys($array, $recursive, StringUtil::toSnakeCase(...));
    }

    /**
     * Convert all string keys to camelCase.
     *
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     *
     * @throws InvalidArgumentException If two keys convert to the same key, or a key is not valid UTF-8.
     * @see self::keyToSnakeCase()
     */
    public static function keyToCamelCase(array $array, bool $recursive = false): array
    {
        return self::convertKeys($array, $recursive, StringUtil::toCamelCase(...));
    }

    /**
     * Convert all string keys to kebab-case.
     *
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     *
     * @throws InvalidArgumentException If two keys convert to the same key, or a key is not valid UTF-8.
     * @see self::keyToSnakeCase()
     */
    public static function keyToKebabCase(array $array, bool $recursive = false): array
    {
        return self::convertKeys($array, $recursive, StringUtil::toKebabCase(...));
    }

    /**
     * Convert all string keys to PascalCase.
     *
     * @param array<array-key, mixed> $array
     * @return array<array-key, mixed>
     *
     * @throws InvalidArgumentException If two keys convert to the same key, or a key is not valid UTF-8.
     * @see self::keyToSnakeCase()
     */
    public static function keyToPascalCase(array $array, bool $recursive = false): array
    {
        return self::convertKeys($array, $recursive, StringUtil::toPascalCase(...));
    }

    /**
     * Merge `$b` into `$a` recursively and return the result.
     *
     * - A string key present in both arrays is merged recursively when both values are arrays;
     *   otherwise the value from `$b` wins (including `null`).
     * - An integer key is appended to the result when `$preserveNumericKeys` is false, so lists
     *   concatenate. When it is true the integer key is treated like a string key.
     * - A key present only in `$b` is added.
     * - Numeric-string keys such as `'10'` are integer keys in PHP, so they are appended by default; pass
     *   `$preserveNumericKeys = true` to merge ID-keyed maps.
     * - By default a nested array under an integer key is appended whole, never merged.
     * - Recursion depth is bounded only by PHP's stack, and arrays containing reference cycles are not
     *   supported.
     *
     * @param array<array-key, mixed> $a
     * @param array<array-key, mixed> $b
     * @return array<array-key, mixed>
     *
     * @throws InvalidArgumentException If a value cannot be appended because `$a` already has the key
     *     `PHP_INT_MAX`.
     */
    public static function merge(array $a, array $b, bool $preserveNumericKeys = false): array
    {
        foreach ($b as $key => $value) {
            if (is_int($key) && !$preserveNumericKeys) {
                if (array_key_exists(PHP_INT_MAX, $a)) {
                    throw new InvalidArgumentException(
                        'Cannot append a value: the first array already uses the largest integer key.',
                    );
                }

                $a[] = $value;
            } elseif (is_array($value) && array_key_exists($key, $a) && is_array($a[$key])) {
                $a[$key] = self::merge($a[$key], $value, $preserveNumericKeys);
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }

    /**
     * Apply `$convert` to every string key, optionally at every depth, preserving key order.
     *
     * A converted key that is numeric, such as `1_` becoming `1`, is stored as an integer key by PHP.
     *
     * @param array<array-key, mixed> $array
     * @param Closure(string): string $convert
     * @return array<array-key, mixed>
     *
     * @throws InvalidArgumentException If two keys convert to the same key.
     */
    private static function convertKeys(array $array, bool $recursive, Closure $convert): array
    {
        $result = [];
        $origins = [];

        foreach ($array as $key => $value) {
            $newKey = is_string($key) ? $convert($key) : $key;

            if (array_key_exists($newKey, $result)) {
                throw new InvalidArgumentException(sprintf(
                    'Keys "%s" and "%s" both convert to "%s".',
                    $origins[$newKey],
                    $key,
                    $newKey,
                ));
            }

            $origins[$newKey] = $key;

            $result[$newKey] = $recursive && is_array($value) ? self::convertKeys($value, true, $convert) : $value;
        }

        return $result;
    }
}
