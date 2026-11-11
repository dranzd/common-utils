# ArrayUtil

Converts array keys between naming styles (using [StringUtil](string-util.md)) and merges arrays
recursively. No method modifies its arguments.

## keyToSnakeCase, keyToCamelCase, keyToKebabCase, keyToPascalCase

```php
public static function keyToSnakeCase(array $array, bool $recursive = false): array
public static function keyToCamelCase(array $array, bool $recursive = false): array
public static function keyToKebabCase(array $array, bool $recursive = false): array
public static function keyToPascalCase(array $array, bool $recursive = false): array
```

- Integer keys are left untouched, so lists stay lists. String keys are converted.
- Key order is preserved.
- With `$recursive` set, nested arrays are converted at every depth. Without it only the top-level
  keys change. Non-array values, including objects, are returned as they are.
- A converted key that is numeric (for example `1_` becoming `1`) is stored as an integer key,
  because that is how PHP treats numeric string keys.

```php
ArrayUtil::keyToCamelCase(['first_name' => 'Ada', 'address_list' => [['zip_code' => 1]], 5 => 'x'], true);
// ['firstName' => 'Ada', 'addressList' => [['zipCode' => 1]], 5 => 'x']

ArrayUtil::keyToCamelCase(['first_name' => 'Ada', 'address_list' => [['zip_code' => 1]]]);
// ['firstName' => 'Ada', 'addressList' => [['zip_code' => 1]]]
```

Throws `\InvalidArgumentException` when two keys end up as the same key (for example `firstName` and
`first_name`, or a converted key and an existing key), rather than silently dropping a value. Also
throws when a key is not valid UTF-8.

```php
ArrayUtil::keyToSnakeCase(['firstName' => 1, 'first_name' => 2]);
// InvalidArgumentException: Keys "firstName" and "first_name" both convert to "first_name".
```

## merge

```php
public static function merge(array $a, array $b, bool $preserveNumericKeys = false): array
```

Merges `$b` into `$a` and returns the result.

- A string key present in both arrays whose values are both arrays is merged recursively (the flag is
  passed down). Otherwise the value from `$b` wins, including `null`.
- An integer key in `$b`: when `$preserveNumericKeys` is false the value is appended (`$a[] = $value`),
  so lists concatenate. When true the key is handled like a string key, so equal indexes are
  overwritten or merged recursively.
- A key present only in `$b` is added.
- Numeric-string keys are integer keys in PHP, so by default `merge(['10' => 'a'], ['10' => 'b'])` gives
  `[10 => 'a', 11 => 'b']`. Maps keyed by ID need `$preserveNumericKeys = true`.
- In the default mode a nested array under an integer key is appended whole, not merged. With `true`,
  arrays at equal integer keys are merged recursively.
- Recursion depth is bounded only by PHP's stack, and arrays containing reference cycles are not
  supported.

```php
ArrayUtil::merge(['a' => [1, 2], 'b' => ['x' => 1]], ['a' => [3], 'b' => ['y' => 2]]);
// ['a' => [1, 2, 3], 'b' => ['x' => 1, 'y' => 2]]

ArrayUtil::merge([1, 2], [3]);        // [1, 2, 3]
ArrayUtil::merge([1, 2], [3], true);  // [3, 2]
ArrayUtil::merge(['a' => 1], ['a' => null]); // ['a' => null]
```

```php
ArrayUtil::merge(['10' => 'a'], ['10' => 'b']);          // [10 => 'a', 11 => 'b']
ArrayUtil::merge(['10' => 'a'], ['10' => 'b'], true);    // [10 => 'b']
ArrayUtil::merge([['a' => 1]], [['b' => 2]]);            // [['a' => 1], ['b' => 2]]
ArrayUtil::merge([['a' => 1]], [['b' => 2]], true);      // [['a' => 1, 'b' => 2]]
```

Throws `\InvalidArgumentException` when a value must be appended but `$a` already has the key
`PHP_INT_MAX` (PHP cannot add another element).
