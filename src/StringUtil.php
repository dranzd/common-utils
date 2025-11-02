<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

/**
 * String utility functions for case conversion.
 * 
 * Provides framework-agnostic string manipulation utilities using native PHP.
 */
final class StringUtil
{
    /**
     * Convert a string to snake_case.
     *
     * @param string $string The input string
     * @return string The string in snake_case format
     */
    public static function toSnakeCase(string $string): string
    {
        // Handle already snake_cased strings
        if (ctype_lower($string)) {
            return $string;
        }

        // Convert camelCase or PascalCase to snake_case
        $string = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $string);
        $string = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $string);
        
        return strtolower($string);
    }

    /**
     * Convert a string to camelCase.
     *
     * @param string $string The input string
     * @return string The string in camelCase format
     */
    public static function toCamelCase(string $string): string
    {
        // Replace underscores and hyphens with spaces, then capitalize words
        $string = str_replace(['-', '_'], ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        
        // Ensure first character is lowercase
        return lcfirst($string);
    }

    /**
     * Convert a string to kebab-case.
     *
     * @param string $string The input string
     * @return string The string in kebab-case format
     */
    public static function toKebabCase(string $string): string
    {
        // Handle already kebab-cased strings
        if (ctype_lower($string) && !str_contains($string, '_')) {
            return $string;
        }

        // Convert camelCase or PascalCase to kebab-case
        $string = preg_replace('/([a-z\d])([A-Z])/', '$1-$2', $string);
        $string = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1-$2', $string);
        
        // Replace underscores with hyphens
        $string = str_replace('_', '-', $string);
        
        return strtolower($string);
    }
}
