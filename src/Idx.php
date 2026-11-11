<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use InvalidArgumentException;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;

/**
 * Safe lookup of values in nested arrays and objects.
 */
final class Idx
{
    /**
     * Returns the value found under a key or path, or $default if any segment is missing.
     *
     * A list `$key` is a path, resolved left to right; an empty path returns $default. Every segment
     * is validated before the walk starts, so a bad segment throws even after a missing one.
     * A stored `null` is a found value and is returned as is.
     *
     * Each segment is resolved against the current subject by the first rule that finds it:
     *  1. array key
     *  2. `\ArrayAccess` offset; an offset that does not exist, or that the object rejects with a
     *     `\TypeError` (e.g. a string key on `\SplObjectStorage`), counts as not found and the
     *     rules below are tried next
     *  3. public property, declared or dynamic
     *  4. magic `__isset()` / `__get()`; both must exist, and a property whose value is null reads
     *     as missing because `__isset()` is false for it
     *  5. accessor, tried in this order: `getKey()`, `isKey()`, `hasKey()`, `key()`. It must be public,
     *     non-static, take no required arguments and not start with `__`. It is only called when a
     *     non-public, non-static property with exactly that name is declared in the class hierarchy,
     *     so `Idx::get($object, 'delete')` never calls `delete()`
     *  6. that non-public property, read via reflection (this deliberately breaks encapsulation so
     *     value objects can be read without getters; an uninitialized typed property counts as missing)
     *
     * Static properties are never read. Exceptions thrown by user code (`__get()`, accessors,
     * `offsetGet()`, `\Countable::count()`) propagate.
     *
     * With `$useDefaultOnEmpty`, a found value that is empty returns $default. Empty means
     * `null`, `''`, `[]` or a `\Countable` with count 0; unlike PHP's `empty()`, the values
     * `0`, `'0'` and `false` are real values and are returned.
     *
     * @param int|string|array<array-key, mixed> $key A single key, or a path of int|string segments.
     * @throws InvalidArgumentException If a path segment is neither an int nor a string.
     */
    public static function get(
        mixed $subject,
        int|string|array $key,
        mixed $default = null,
        bool $useDefaultOnEmpty = false
    ): mixed {
        $path = is_array($key) ? $key : [$key];

        foreach ($path as $segment) {
            if (!is_int($segment) && !is_string($segment)) {
                throw new InvalidArgumentException(sprintf(
                    'Path segments must be int or string, %s given.',
                    get_debug_type($segment)
                ));
            }
        }

        if ($path === []) {
            return $default;
        }

        $value = $subject;

        foreach ($path as $segment) {
            if (!self::find($value, $segment, $value)) {
                return $default;
            }
        }

        return $useDefaultOnEmpty && self::isEmpty($value) ? $default : $value;
    }

    /**
     * Resolves one segment against a subject; returns whether it was found.
     */
    private static function find(mixed $subject, int|string $key, mixed &$found): bool
    {
        if (is_array($subject)) {
            if (!array_key_exists($key, $subject)) {
                return false;
            }
            $found = $subject[$key];

            return true;
        }

        if (!is_object($subject)) {
            return false;
        }

        return ($subject instanceof \ArrayAccess && self::readOffset($subject, $key, $found))
            || self::readObject($subject, (string) $key, $found);
    }

    /**
     * @param \ArrayAccess<mixed, mixed> $subject
     */
    private static function readOffset(\ArrayAccess $subject, int|string $key, mixed &$found): bool
    {
        try {
            if (!$subject->offsetExists($key)) {
                return false;
            }

            $found = $subject->offsetGet($key);
        } catch (\TypeError) {
            return false;
        }

        return true;
    }

    private static function readObject(object $subject, string $name, mixed &$found): bool
    {
        $public = get_object_vars($subject);

        if (array_key_exists($name, $public)) {
            $found = $public[$name];

            return true;
        }

        if (method_exists($subject, '__isset') && method_exists($subject, '__get') && (bool) $subject->__isset($name)) {
            $found = $subject->__get($name);

            return true;
        }

        $property = self::findNonPublic($subject, $name);

        if ($property === null) {
            return false;
        }

        if (self::callAccessor($subject, $name, $found)) {
            return true;
        }

        if (!$property->isInitialized($subject)) {
            return false;
        }

        $found = $property->getValue($subject);

        return true;
    }

    private static function findNonPublic(object $subject, string $name): ?ReflectionProperty
    {
        $class = new ReflectionObject($subject);

        while ($class !== false) {
            if ($class->hasProperty($name) && !$class->getProperty($name)->isStatic()) {
                $property = $class->getProperty($name);

                return $property->isPublic() ? null : $property;
            }

            $class = $class->getParentClass();
        }

        return null;
    }

    private static function callAccessor(object $subject, string $name, mixed &$found): bool
    {
        if (str_starts_with($name, '__')) {
            return false;
        }

        foreach (['get' . ucfirst($name), 'is' . ucfirst($name), 'has' . ucfirst($name), $name] as $method) {
            if (
                method_exists($subject, $method)
                && ($reflection = new ReflectionMethod($subject, $method))->isPublic()
                && !$reflection->isStatic()
                && $reflection->getNumberOfRequiredParameters() === 0
            ) {
                $found = $reflection->invoke($subject);

                return true;
            }
        }

        return false;
    }

    private static function isEmpty(mixed $value): bool
    {
        return $value === null
            || $value === ''
            || $value === []
            || ($value instanceof \Countable && count($value) === 0);
    }
}
