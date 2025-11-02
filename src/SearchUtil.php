<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

/**
 * Search utility functions for SQL LIKE pattern generation.
 *
 * Provides framework-agnostic search utilities using native PHP.
 */
final class SearchUtil
{
    /**
     * Convert filter to loose pattern with wildcards on both ends.
     *
     * @param string $filter The search filter
     * @return string Pattern with wildcards: %filter%
     */
    public static function toLooseIncludeEnds(string $filter): string
    {
        return '%' . static::toLoose($filter) . '%';
    }

    /**
     * Convert filter to loose pattern with wildcard on the left.
     *
     * @param string $filter The search filter
     * @return string Pattern with left wildcard: %filter
     */
    public static function toLooseIncludeLeft(string $filter): string
    {
        return '%' . static::toLoose($filter);
    }

    /**
     * Convert filter to loose pattern with wildcard on the right.
     *
     * @param string $filter The search filter
     * @return string Pattern with right wildcard: filter%
     */
    public static function toLooseIncludeRight(string $filter): string
    {
        return static::toLoose($filter) . '%';
    }

    /**
     * Convert filter to loose pattern by replacing non-graphical characters with wildcards.
     *
     * @param string $filter The search filter
     * @return string Loose pattern
     */
    public static function toLoose(string $filter): string
    {
        return (string) preg_replace('/[^[:graph:]]+/i', '%', $filter);
    }
}
