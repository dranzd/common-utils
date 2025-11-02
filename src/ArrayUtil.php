<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

/**
 * Array utility functions for key manipulation and merging.
 * 
 * Provides framework-agnostic array manipulation utilities using native PHP.
 */
final class ArrayUtil
{
    /**
     * Convert all array keys to snake_case.
     *
     * @param array $array The input array
     * @param bool $recursive Whether to recursively convert nested arrays
     * @return array The array with snake_case keys
     */
    public static function keyToSnakeCase(array $array, bool $recursive = false): array
    {
        $keys = array_keys($array);
        $values = array_values($array);

        $keys = array_map([StringUtil::class, 'toSnakeCase'], $keys);

        $array = array_combine($keys, $values);

        if (!$recursive) {
            return $array;
        }

        return array_map(
            function ($item) use ($recursive) {
                if (!is_array($item)) {
                    return $item;
                }

                return self::keyToSnakeCase($item, $recursive);
            },
            $array
        );
    }

    /**
     * Convert all array keys to camelCase.
     *
     * @param array $array The input array
     * @param bool $recursive Whether to recursively convert nested arrays
     * @return array The array with camelCase keys
     */
    public static function keyToCamelCase(array $array, bool $recursive = false): array
    {
        $keys = array_keys($array);
        $values = array_values($array);

        $keys = array_map([StringUtil::class, 'toCamelCase'], $keys);

        $array = array_combine($keys, $values);

        if (!$recursive) {
            return $array;
        }

        return array_map(
            function ($item) use ($recursive) {
                if (!is_array($item)) {
                    return $item;
                }

                return self::keyToCamelCase($item, $recursive);
            },
            $array
        );
    }

    /**
     * Convert all array keys to kebab-case.
     *
     * @param array $array The input array
     * @param bool $recursive Whether to recursively convert nested arrays
     * @return array The array with kebab-case keys
     */
    public static function keyToKebabCase(array $array, bool $recursive = false): array
    {
        $keys = array_keys($array);
        $values = array_values($array);

        $keys = array_map([StringUtil::class, 'toKebabCase'], $keys);

        $array = array_combine($keys, $values);

        if (!$recursive) {
            return $array;
        }

        return array_map(
            function ($item) use ($recursive) {
                if (!is_array($item)) {
                    return $item;
                }

                return self::keyToKebabCase($item, $recursive);
            },
            $array
        );
    }

    /**
     * Merge two arrays recursively.
     *
     * @param array $a First array
     * @param array $b Second array
     * @param bool $preserveNumericKeys Whether to preserve numeric keys (default: false)
     * @return array The merged array
     */
    public static function merge(array $a, array $b, bool $preserveNumericKeys = false): array
    {
        if ($preserveNumericKeys) {
            return self::mergePreservingNumericKeys($a, $b);
        }

        return array_replace_recursive($a, $b);
    }

    /**
     * Merge arrays while preserving numeric keys.
     *
     * @param array $a First array
     * @param array $b Second array
     * @return array The merged array
     */
    private static function mergePreservingNumericKeys(array $a, array $b): array
    {
        foreach ($b as $key => $value) {
            if (is_int($key)) {
                $a[$key] = $value;
            } elseif (is_array($value) && isset($a[$key]) && is_array($a[$key])) {
                $a[$key] = self::mergePreservingNumericKeys($a[$key], $value);
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }
}
