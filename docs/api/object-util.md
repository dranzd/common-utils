# ObjectUtil

Static reflection helpers for reading and writing object properties regardless of visibility.
Properties are looked up on the object's class and then up its parent chain, so private properties
declared on any ancestor are reachable; existing dynamic properties work too, but setting a new dynamic
property throws. Static properties are not instance state and are ignored. The nearest declaration
wins: a private property redeclared in a child class hides the parent's property of the same name, and
these helpers cannot reach the parent's. They deliberately bypass encapsulation, so use them for
hydration, test setup and similar infrastructure code.

## getPropertyValue

```php
public static function getPropertyValue(object $object, string $property): mixed
```

Reads a property of any visibility at any level of the hierarchy.

Throws `\InvalidArgumentException` if the property does not exist, or is a typed property that is not
yet initialized.

## setPropertyValue

```php
public static function setPropertyValue(object $object, string $property, mixed $value): void
```

Writes a property with the same lookup rules.

- Values are assigned with PHP's non-strict coercion rules: `'5'` written to an `int` property stores
  `5`. A value the property cannot hold throws.
- A float with a fractional part written to an `int` property (nullable or in a union) that does not
  also accept `float` throws, instead of being truncated with a deprecation notice.
- Writing an initialized `readonly` property throws. An uninitialized one can be written, which is
  what hydrating an instance from `newInstanceWithoutConstructor()` needs.
- Engine errors (`\TypeError`, the readonly violation) are the previous exception.

Throws `\InvalidArgumentException` if the property does not exist or the write is rejected.

## hasProperty

```php
public static function hasProperty(object $object, string $property): bool
```

True if the property is declared at any level of the hierarchy or exists dynamically on the object.

## newInstanceWithoutConstructor

```php
/**
 * @template T of object
 * @param class-string<T> $className
 * @return T
 */
public static function newInstanceWithoutConstructor(string $className): object
```

Creates an instance without calling its constructor.

Throws `\InvalidArgumentException`, with a message that says which case it is, for an unknown name, an
interface, a trait, an abstract class, an enum, or an internal class that cannot be instantiated this way
(the `\ReflectionException` is the previous exception). Anonymous class names contain a NUL byte and a
path; messages show only the part before it, such as `class@anonymous`.

## Example

```php
class A { private string $secret = 's'; }
class B extends A {}

$b = new B();
ObjectUtil::getPropertyValue($b, 'secret');        // "s" (private, declared on the parent)
ObjectUtil::setPropertyValue($b, 'secret', 't');
ObjectUtil::getPropertyValue($b, 'secret');        // "t"
ObjectUtil::hasProperty($b, 'secret');             // true
ObjectUtil::hasProperty($b, 'x');                  // false
ObjectUtil::getPropertyValue($b, 'x');             // throws: Property B::$x does not exist.

final class V { public function __construct(public readonly int $n) {} }
final class W { public int $count = 0; }
$w = new W();
$v = ObjectUtil::newInstanceWithoutConstructor(V::class);
ObjectUtil::setPropertyValue($v, 'n', 2);          // $v->n is now 2
ObjectUtil::setPropertyValue($v, 'n', 3);
// throws: Cannot set property V::$n: Cannot modify readonly property V::$n

ObjectUtil::setPropertyValue($w, 'count', '5');    // stores int 5 ($w has `int $count`)
ObjectUtil::setPropertyValue($w, 'count', 1.5);
// throws: Cannot set property W::$count: float 1.5 would lose its fractional part.
```

## Deprecated

Kept for code written against the former singleton API; both will be removed in 2.0.

### instance

```php
public static function instance(): self
```

Returns a shared instance. All methods are static and can still be called on it, so
`ObjectUtil::instance()->getPropertyValue($object, 'name')` keeps working.

### valueOfProperty

```php
public static function valueOfProperty(object $object, string $property): mixed
```

Alias of `getPropertyValue()`, with the same behaviour and exceptions.
