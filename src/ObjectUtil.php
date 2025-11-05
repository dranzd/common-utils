<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

use ReflectionClass;
use ReflectionProperty;

/**
 * Object utility functions for reflection and manipulation.
 *
 * Provides framework-agnostic object manipulation utilities using native PHP.
 */
final class ObjectUtil
{
    private static ?self $instance = null;

    private function __construct()
    {
        // nothing here
    }

    /**
     * @static
     * @return self
     */
    public static function instance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @see self::valueOfProperty
     * @return mixed
     */
    public function getPropertyValue(): mixed
    {
        return \call_user_func_array([$this, 'valueOfProperty'], \func_get_args());
    }

    /**
     * @param  object               $object
     * @param  string               $property
     * @param  ReflectionClass|null $reflection
     * @return mixed
     */
    public function valueOfProperty(object $object, string $property, ?ReflectionClass $reflection = null): mixed
    {
        if (null === $reflection) {
            $reflection = new \ReflectionClass(\get_class($object));
        }

        $property = $reflection->getProperty($property);

        $value = null;

        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            $property->setAccessible(false);
        } else {
            $value = $property->getValue($object);
        }

        return $value;
    }

    /**
     * @param  object               $object
     * @param  string               $property
     * @param  mixed                $value
     * @param  ReflectionClass|null $reflection
     */
    public function setPropertyValue(
        object $object,
        string $property,
        mixed $value,
        ?ReflectionClass $reflection = null
    ): void {
        if (null === $reflection) {
            $reflection = new \ReflectionClass(\get_class($object));
        }

        /** @var \ReflectionClass $reflection */

        $reflectionProperty = null;
        if ($reflection->hasProperty($property)) {
            $reflectionProperty = $reflection->getProperty($property);
        } else {
            $parentClass = \get_parent_class($object);
            if ($parentClass !== false) {
                $parentReflection = new \ReflectionClass($parentClass);
                if ($parentReflection->hasProperty($property)) {
                    $reflectionProperty = $parentReflection->getProperty($property);
                }
            }
        }

        if ($reflectionProperty === null) {
            return;
        }

        $isAccessible = !$reflectionProperty->isPrivate() && !$reflectionProperty->isProtected();

        if (!$isAccessible) {
            $reflectionProperty->setAccessible(true);
        }

        $reflectionProperty->setValue($object, $value);

        if (!$isAccessible) {
            $reflectionProperty->setAccessible(false);
        }
    }

    /**
     * @param  string               $className
     * @param  ReflectionClass|null $reflection
     * @return Object|null
     */
    public function newInstanceWithoutConstructor(string $className, ?ReflectionClass $reflection = null): ?object
    {
        if (!class_exists($className)) {
            return null;
        }

        if (null === $reflection) {
            $reflection = new \ReflectionClass($className);
        }

        return $reflection->newInstanceWithoutConstructor();
    }

    // public function extractByClassMethods($object, $recursive = false)
    // {
    //     $extractor = new \Laminas\Hydrator\ClassMethods();
    //     $array     = $extractor->extract($object);

    //     if (!$recursive) {
    //         return $array;
    //     }

    //     foreach ($array as $k => $v) {
    //         if (!is_object($v)) {
    //             continue;
    //         }

    //         $array[$k] = $this->extractByClassMethods($v, $recursive);
    //     }

    //     return $array;
    // }
}
