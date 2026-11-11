<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * Reflection helpers for reading and writing properties regardless of visibility.
 *
 * Properties are looked up on the object's class and then up its parent chain, so private
 * properties declared on any ancestor are reachable. The nearest declaration wins: a private
 * property redeclared in a child class hides the parent's property of the same name, which these
 * helpers cannot reach. Existing dynamic properties are supported too; static properties are not
 * instance state and are ignored.
 * These helpers deliberately bypass encapsulation; use them for hydration, testing and
 * similar infrastructure code, not for regular application logic.
 */
final class ObjectUtil
{
    private static ?self $instance = null;

    /**
     * Reads a property of any visibility, declared at any level of the class hierarchy.
     *
     * @throws InvalidArgumentException If the property does not exist or is uninitialized.
     */
    public static function getPropertyValue(object $object, string $property): mixed
    {
        $reflection = self::findDeclared($object, $property);

        if ($reflection === null) {
            throw self::missing($object, $property);
        }

        if (!$reflection->isInitialized($object)) {
            throw new InvalidArgumentException(sprintf(
                'Property %s::$%s must not be accessed before initialization.',
                $object::class,
                $property
            ));
        }

        return $reflection->getValue($object);
    }

    /**
     * Writes a property of any visibility, declared at any level of the class hierarchy.
     *
     * The value is assigned with PHP's non-strict coercion rules, so a numeric string is stored in an
     * `int` property as an int. A value the property cannot hold throws, as does writing an
     * initialized `readonly` property. A float with a fractional part written to an `int` property
     * that does not also accept float throws too, instead of being truncated with a deprecation.
     * The engine's error, when there is one, is the previous exception.
     *
     * @throws InvalidArgumentException If the property does not exist or the write is rejected.
     */
    public static function setPropertyValue(object $object, string $property, mixed $value): void
    {
        $reflection = self::findDeclared($object, $property);

        if ($reflection === null) {
            throw self::missing($object, $property);
        }

        if (self::losesPrecision($reflection->getType(), $value)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot set property %s::$%s: float %s would lose its fractional part.',
                $object::class,
                $property,
                var_export($value, true)
            ));
        }

        try {
            $reflection->setValue($object, $value);
        } catch (\Error $error) {
            $rejected = $error instanceof \TypeError || $reflection->isReadOnly();

            throw $rejected ? new InvalidArgumentException(sprintf(
                'Cannot set property %s::$%s: %s',
                $object::class,
                $property,
                $error->getMessage()
            ), 0, $error) : $error;
        }
    }

    /**
     * Tells whether the property is declared at any level of the hierarchy or exists dynamically.
     */
    public static function hasProperty(object $object, string $property): bool
    {
        return self::findDeclared($object, $property) !== null;
    }

    /**
     * Creates an instance without invoking its constructor.
     *
     * @template T of object
     * @param class-string<T> $className
     * @return T
     * @throws InvalidArgumentException If the name is not a class, or the class is abstract, an enum,
     *                                  or an internal class that cannot be instantiated this way.
     */
    public static function newInstanceWithoutConstructor(string $className): object
    {
        $label = explode("\0", $className, 2)[0];

        if (!class_exists($className)) {
            throw new InvalidArgumentException(match (true) {
                interface_exists($className, false) => sprintf('"%s" is an interface, not a class.', $label),
                trait_exists($className, false) => sprintf('"%s" is a trait, not a class.', $label),
                default => sprintf('Class "%s" does not exist.', $label),
            });
        }

        $reflection = new ReflectionClass($className);

        if ($reflection->isEnum()) {
            throw new InvalidArgumentException(sprintf('"%s" is an enum and cannot be instantiated.', $label));
        }

        if ($reflection->isAbstract()) {
            throw new InvalidArgumentException(sprintf('Class "%s" is abstract and cannot be instantiated.', $label));
        }

        try {
            return $reflection->newInstanceWithoutConstructor();
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(sprintf(
                'Internal class "%s" cannot be instantiated without its constructor.',
                $label
            ), 0, $exception);
        }
    }

    /**
     * Returns a shared instance, for code written against the former singleton API.
     *
     * Every method is static and can still be called on the returned instance, so
     * `ObjectUtil::instance()->getPropertyValue($object, 'name')` keeps working.
     *
     * @deprecated since 1.0, will be removed in 2.0. Call the static methods directly.
     */
    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * Alias of {@see self::getPropertyValue()}.
     *
     * @deprecated since 1.0, will be removed in 2.0. Use getPropertyValue() instead.
     *
     * @throws InvalidArgumentException If the property does not exist or is uninitialized.
     */
    public static function valueOfProperty(object $object, string $property): mixed
    {
        return self::getPropertyValue($object, $property);
    }

    private static function findDeclared(object $object, string $property): ?ReflectionProperty
    {
        $class = new ReflectionObject($object);

        while ($class !== false) {
            $declared = $class->hasProperty($property) ? $class->getProperty($property) : null;

            if ($declared !== null && !$declared->isStatic()) {
                return $declared;
            }

            $class = $class->getParentClass();
        }

        return null;
    }

    /**
     * Whether a coercing write would silently drop the fraction of a float: PHP 8.1+ only deprecates it.
     */
    private static function losesPrecision(?ReflectionType $type, mixed $value): bool
    {
        if (!is_float($value) || !is_finite($value) || fmod($value, 1.0) === 0.0) {
            return false;
        }

        $names = [];

        foreach ($type instanceof ReflectionUnionType ? $type->getTypes() : [$type] as $member) {
            if ($member instanceof ReflectionNamedType) {
                $names[] = $member->getName();
            }
        }

        return in_array('int', $names, true) && !in_array('float', $names, true);
    }

    private static function missing(object $object, string $property): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf('Property %s::$%s does not exist.', $object::class, $property));
    }
}
