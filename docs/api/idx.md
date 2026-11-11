# Idx

Safe lookup of values in nested arrays and objects. Instead of chains of `isset()` checks, `Idx::get()`
walks a key or a path and returns a default when any segment is missing.

## get

```php
public static function get(
    mixed $subject,
    int|string|array $key,
    mixed $default = null,
    bool $useDefaultOnEmpty = false
): mixed
```

- `$key` is a single key, or a path (a list of `int|string` segments) resolved left to right.
- If any segment is missing, `$default` is returned. A stored `null` counts as found and is returned.
- An empty path (`[]`) returns `$default`.
- Every path segment is validated before the walk, so a bad segment throws even after a missing one.

Each segment is resolved against the current subject by the first rule that finds it:

1. Array: the key exists (`array_key_exists`).
2. `\ArrayAccess` (including `\ArrayObject`): `offsetExists()` then `offsetGet()`. If the offset does not
   exist, or the object rejects the key with a `\TypeError` (a string key on `\SplObjectStorage`,
   `\WeakMap` or `\SplFixedArray`), it counts as not found and the next rules are tried.
3. Public property, declared or dynamic. Integer keys are cast to strings.
4. Magic `__isset()` / `__get()`: both must exist; if `__isset()` is true, `__get()` is returned. A
   magic property set to `null` therefore reads as missing, because `__isset()` is false for it.
5. Accessor method, tried in this order: `getKey()`, `isKey()`, `hasKey()`, `key()`. It must be public,
   non-static, take no required parameters and not start with `__`. It is called only when a
   non-public, non-static property with exactly that name is declared in the class hierarchy; a method
   with no property behind it is never called, so `Idx::get($object, 'delete')` cannot run `delete()`.
6. That non-public property, read via reflection. This deliberately breaks encapsulation so value
   objects can be read without getters. An uninitialized typed property counts as missing.
7. Anything else (`null`, scalars, nothing matched): not found.

Because of the order, an offset beats a public property, a public property beats magic, magic beats an
accessor, and an accessor beats the private property behind it. Static properties are never read.

Exceptions thrown by user code (`__get()`, accessors, `offsetGet()`, `\Countable::count()`) propagate.

### Empty values

With `$useDefaultOnEmpty = true`, a found value that is empty returns `$default`. Empty means `null`,
`''`, `[]`, or a `\Countable` with count 0. This is not PHP's `empty()`: `0`, `'0'` and `false`
are real values and are returned.

### Example

```php
final class Money
{
    public function __construct(private int $amount, private string $currency) {}

    public function currency(): string
    {
        return strtolower($this->currency);
    }
}

$order = [
    'customer' => ['address' => ['city' => 'Oslo']],
    'total' => new Money(500, 'NOK'),
    'note' => '',
    'qty' => 0,
];

Idx::get($order, ['customer', 'address', 'city']);  // "Oslo"
Idx::get($order, ['customer', 'phone'], 'n/a');     // "n/a"
Idx::get($order, ['total', 'amount']);              // 500 (private property, via reflection)
Idx::get($order, ['total', 'currency']);            // "nok" (accessor method)
Idx::get($order, 'note', 'none', true);             // "none"
Idx::get($order, 'qty', 1, true);                   // 0
Idx::get(new ArrayObject(['a' => 1]), 'a', 'd');    // 1

final class Doc
{
    private string $title = 'draft';

    public function delete(): void {}
}

Idx::get(new Doc(), 'title');                       // "draft"
Idx::get(new Doc(), 'delete', 'd');                 // "d" (no property "delete", so never called)
Idx::get(new SplObjectStorage(), 'a', 'd');         // "d" (the TypeError is treated as not found)
```

### Throws

- `\InvalidArgumentException` if a path segment is not an `int` or a `string`.
