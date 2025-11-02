# ArrayUtil API Reference

## Overview

`ArrayUtil` provides framework-agnostic array manipulation utilities, primarily focused on key case conversion and array merging operations.

**Namespace:** `Dranzd\Common\Utils\ArrayUtil`

## Methods

### keyToSnakeCase()

Convert all array keys to snake_case format.

```php
public static function keyToSnakeCase(array $array, bool $recursive = false): array
```

#### Parameters

- `array $array` - The input array
- `bool $recursive` - Whether to recursively convert nested arrays (default: `false`)

#### Returns

`array` - The array with all keys converted to snake_case

#### Examples

```php
use Dranzd\Common\Utils\ArrayUtil;

// Basic conversion
$data = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'emailAddress' => 'john@example.com'
];

$result = ArrayUtil::keyToSnakeCase($data);
// Result: ['first_name' => 'John', 'last_name' => 'Doe', 'email_address' => 'john@example.com']

// Recursive conversion
$nested = [
    'userInfo' => [
        'firstName' => 'John',
        'contactDetails' => [
            'emailAddress' => 'john@example.com',
            'phoneNumber' => '555-1234'
        ]
    ]
];

$result = ArrayUtil::keyToSnakeCase($nested, true);
// Result: All nested keys converted to snake_case
```

#### Use Cases

- Converting API responses from camelCase to snake_case for database storage
- Normalizing data from different sources
- Preparing data for systems that expect snake_case keys

---

### keyToCamelCase()

Convert all array keys to camelCase format.

```php
public static function keyToCamelCase(array $array, bool $recursive = false): array
```

#### Parameters

- `array $array` - The input array
- `bool $recursive` - Whether to recursively convert nested arrays (default: `false`)

#### Returns

`array` - The array with all keys converted to camelCase

#### Examples

```php
use Dranzd\Common\Utils\ArrayUtil;

// Basic conversion
$data = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email_address' => 'john@example.com'
];

$result = ArrayUtil::keyToCamelCase($data);
// Result: ['firstName' => 'John', 'lastName' => 'Doe', 'emailAddress' => 'john@example.com']

// Recursive conversion
$nested = [
    'user_info' => [
        'first_name' => 'John',
        'contact_details' => [
            'email_address' => 'john@example.com'
        ]
    ]
];

$result = ArrayUtil::keyToCamelCase($nested, true);
// Result: All nested keys converted to camelCase
```

#### Use Cases

- Converting database results to camelCase for API responses
- Transforming data for JavaScript/JSON APIs
- Normalizing data from snake_case systems

---

### keyToKebabCase()

Convert all array keys to kebab-case format.

```php
public static function keyToKebabCase(array $array, bool $recursive = false): array
```

#### Parameters

- `array $array` - The input array
- `bool $recursive` - Whether to recursively convert nested arrays (default: `false`)

#### Returns

`array` - The array with all keys converted to kebab-case

#### Examples

```php
use Dranzd\Common\Utils\ArrayUtil;

// Basic conversion
$data = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'emailAddress' => 'john@example.com'
];

$result = ArrayUtil::keyToKebabCase($data);
// Result: ['first-name' => 'John', 'last-name' => 'Doe', 'email-address' => 'john@example.com']

// Recursive conversion
$nested = [
    'userInfo' => [
        'firstName' => 'John',
        'contactDetails' => [
            'emailAddress' => 'john@example.com'
        ]
    ]
];

$result = ArrayUtil::keyToKebabCase($nested, true);
// Result: All nested keys converted to kebab-case
```

#### Use Cases

- Preparing data for HTML attributes
- Converting data for CSS-in-JS systems
- Normalizing data for kebab-case systems

---

### merge()

Merge two arrays recursively with optional numeric key preservation.

```php
public static function merge(array $a, array $b, bool $preserveNumericKeys = false): array
```

#### Parameters

- `array $a` - First array
- `array $b` - Second array
- `bool $preserveNumericKeys` - Whether to preserve numeric keys (default: `false`)

#### Returns

`array` - The merged array

#### Examples

```php
use Dranzd\Common\Utils\ArrayUtil;

// Basic merge (numeric keys are re-indexed)
$array1 = ['a' => 1, 'b' => 2];
$array2 = ['b' => 3, 'c' => 4];

$result = ArrayUtil::merge($array1, $array2);
// Result: ['a' => 1, 'b' => 3, 'c' => 4]

// Nested merge
$config1 = [
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
];

$config2 = [
    'database' => [
        'port' => 5432,
        'name' => 'mydb'
    ]
];

$result = ArrayUtil::merge($config1, $config2);
// Result: ['database' => ['host' => 'localhost', 'port' => 5432, 'name' => 'mydb']]

// Preserve numeric keys
$array1 = [0 => 'a', 1 => 'b'];
$array2 = [0 => 'c', 2 => 'd'];

$result = ArrayUtil::merge($array1, $array2, true);
// Result: [0 => 'c', 1 => 'b', 2 => 'd']
```

#### Use Cases

- Merging configuration arrays
- Combining default and user-provided options
- Deep merging of nested data structures

---

## Notes

### Performance Considerations

- Recursive operations on deeply nested arrays may impact performance
- Consider the depth of your data structures when using recursive mode
- For large arrays, non-recursive mode is more efficient

### Type Safety

All methods use strict types (`declare(strict_types=1)`) and require PHP 8.3+.

### Immutability

All methods return new arrays and do not modify the input arrays.

---

## Related Utilities

- [StringUtil](string-util.md) - Used internally for key case conversion
- [Idx](idx.md) - For safe array access

---

**Package:** dranzd/common-utils  
**Version:** 1.0.0  
**PHP:** 8.3+
