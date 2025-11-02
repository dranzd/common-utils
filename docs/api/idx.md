# Idx API Reference

## Overview

`Idx` provides safe array and object property access with default values, preventing undefined index/property errors.

**Namespace:** `Dranzd\Common\Utils\Idx`

## Methods

### get()

Safely retrieve a value from an array or object using a key or array of keys.

```php
public static function get(
    mixed $object, 
    mixed $key, 
    mixed $default = null, 
    bool $useDefaultOnEmpty = false
): mixed
```

#### Parameters

- `mixed $object` - Array or object to access
- `mixed $key` - String, numeric, or array of keys for nested access
- `mixed $default` - Default value to return if key is not found (default: `null`)
- `bool $useDefaultOnEmpty` - Use default value if the value is empty (default: `false`)

#### Returns

`mixed` - The value at the specified key, or the default value

#### Examples

##### Basic Array Access

```php
use Dranzd\Common\Utils\Idx;

$data = [
    'name' => 'John',
    'age' => 30,
    'email' => 'john@example.com'
];

// Existing key
$name = Idx::get($data, 'name');
// Result: 'John'

// Non-existing key with default
$phone = Idx::get($data, 'phone', 'N/A');
// Result: 'N/A'

// Non-existing key without default
$address = Idx::get($data, 'address');
// Result: null
```

##### Nested Array Access

```php
use Dranzd\Common\Utils\Idx;

$data = [
    'user' => [
        'profile' => [
            'name' => 'John',
            'email' => 'john@example.com'
        ],
        'settings' => [
            'theme' => 'dark'
        ]
    ]
];

// Access nested value
$name = Idx::get($data, ['user', 'profile', 'name']);
// Result: 'John'

// Access deep nested value with default
$language = Idx::get($data, ['user', 'settings', 'language'], 'en');
// Result: 'en'

// Partial path exists
$value = Idx::get($data, ['user', 'preferences', 'color'], 'blue');
// Result: 'blue' (preferences doesn't exist)
```

##### Object Property Access

```php
use Dranzd\Common\Utils\Idx;

$user = new stdClass();
$user->name = 'Jane';
$user->email = 'jane@example.com';

// Access public property
$name = Idx::get($user, 'name');
// Result: 'Jane'

// Non-existing property with default
$phone = Idx::get($user, 'phone', 'N/A');
// Result: 'N/A'
```

##### Private/Protected Property Access

```php
use Dranzd\Common\Utils\Idx;

class User {
    private string $id = '123';
    protected string $role = 'admin';
    
    public function getId(): string {
        return $this->id;
    }
}

$user = new User();

// Access via getter method
$id = Idx::get($user, 'id');
// Result: '123' (uses getId() method)

// Access via reflection (if no getter exists)
$role = Idx::get($user, 'role');
// Result: 'admin' (uses reflection)
```

##### Use Default on Empty

```php
use Dranzd\Common\Utils\Idx;

$data = [
    'name' => 'John',
    'email' => '',
    'phone' => null
];

// Normal behavior (returns empty string)
$email = Idx::get($data, 'email', 'no-email@example.com');
// Result: ''

// Use default on empty
$email = Idx::get($data, 'email', 'no-email@example.com', true);
// Result: 'no-email@example.com'

// Works with null values too
$phone = Idx::get($data, 'phone', '555-0000', true);
// Result: '555-0000'
```

##### ArrayObject Support

```php
use Dranzd\Common\Utils\Idx;

$data = new ArrayObject([
    'name' => 'John',
    'age' => 30
]);

$name = Idx::get($data, 'name');
// Result: 'John'

$phone = Idx::get($data, 'phone', 'N/A');
// Result: 'N/A'
```

---

## Use Cases

### Safe Configuration Access

```php
use Dranzd\Common\Utils\Idx;

$config = [
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
];

// Safe access with defaults
$host = Idx::get($config, ['database', 'host'], '127.0.0.1');
$port = Idx::get($config, ['database', 'port'], 3306);
$timeout = Idx::get($config, ['database', 'timeout'], 30);
$charset = Idx::get($config, ['database', 'charset'], 'utf8mb4');

// No need for isset() checks or null coalescing
```

### API Response Handling

```php
use Dranzd\Common\Utils\Idx;

$apiResponse = json_decode($jsonString, true);

// Safely extract nested data
$userId = Idx::get($apiResponse, ['data', 'user', 'id'], 0);
$userName = Idx::get($apiResponse, ['data', 'user', 'name'], 'Unknown');
$userEmail = Idx::get($apiResponse, ['data', 'user', 'email'], 'no-email');

// Handle optional fields
$avatar = Idx::get($apiResponse, ['data', 'user', 'avatar'], '/default-avatar.png');
```

### Form Data Processing

```php
use Dranzd\Common\Utils\Idx;

$formData = $_POST;

// Safe access with defaults
$name = Idx::get($formData, 'name', '');
$email = Idx::get($formData, 'email', '');
$newsletter = Idx::get($formData, 'newsletter', false);

// Use default on empty (treat empty strings as missing)
$phone = Idx::get($formData, 'phone', 'N/A', true);
```

### Object Property Extraction

```php
use Dranzd\Common\Utils\Idx;

function getUserDisplayName($user): string {
    // Try multiple properties with fallbacks
    $name = Idx::get($user, 'displayName');
    if ($name) return $name;
    
    $name = Idx::get($user, 'fullName');
    if ($name) return $name;
    
    $firstName = Idx::get($user, 'firstName', '');
    $lastName = Idx::get($user, 'lastName', '');
    
    return trim("$firstName $lastName") ?: 'Anonymous';
}
```

---

## Implementation Details

### Getter Method Resolution

For private/protected properties, `Idx::get()` attempts to find getter methods in this order:

1. `get{PropertyName}()` - e.g., `getId()` for property `id`
2. `{propertyName}()` - e.g., `id()` for property `id`
3. Reflection access - Direct property access via `ReflectionProperty`

### Reflection Usage

When accessing private/protected properties without getter methods, `Idx` uses PHP's Reflection API:

```php
$reflection = new \ReflectionProperty($object, $key);
$reflection->setAccessible(true);
$value = $reflection->getValue($object);
$reflection->setAccessible(false);
```

This allows safe access to encapsulated data without breaking encapsulation permanently.

### Empty Value Detection

When `$useDefaultOnEmpty` is `true`, the following are considered empty:
- `null`
- `''` (empty string)
- `0` (integer zero)
- `'0'` (string zero)
- `false`
- `[]` (empty array)

Uses PHP's `empty()` function internally.

---

## Performance Considerations

### Nested Access Performance

```php
// Multiple calls (slower)
$value = Idx::get(Idx::get(Idx::get($data, 'a'), 'b'), 'c');

// Single call with array (faster)
$value = Idx::get($data, ['a', 'b', 'c']);
```

### Reflection Overhead

Accessing private/protected properties via reflection has overhead. Consider:
- Adding public getter methods when possible
- Caching reflection objects for repeated access
- Using public properties for frequently accessed data

---

## Type Safety

All methods use strict types (`declare(strict_types=1)`) and require PHP 8.3+.

The method signature uses `mixed` type for maximum flexibility:
- Accepts any type for `$object` (array, object, etc.)
- Accepts any type for `$key` (string, int, array)
- Returns any type based on the stored value

---

## Comparison with Alternatives

### vs. Null Coalescing Operator

```php
// Null coalescing (only works for arrays)
$value = $data['key'] ?? 'default';

// Idx (works for arrays and objects, supports nested access)
$value = Idx::get($data, 'key', 'default');
$value = Idx::get($data, ['a', 'b', 'c'], 'default');
```

### vs. isset() Checks

```php
// Traditional isset (verbose for nested access)
$value = isset($data['a']['b']['c']) ? $data['a']['b']['c'] : 'default';

// Idx (concise)
$value = Idx::get($data, ['a', 'b', 'c'], 'default');
```

### vs. array_key_exists()

```php
// array_key_exists (only for arrays, no default)
if (array_key_exists('key', $data)) {
    $value = $data['key'];
} else {
    $value = 'default';
}

// Idx (works for arrays and objects)
$value = Idx::get($data, 'key', 'default');
```

---

## Edge Cases

### Numeric Keys

```php
$data = ['a', 'b', 'c'];

$value = Idx::get($data, 0);      // 'a'
$value = Idx::get($data, 1);      // 'b'
$value = Idx::get($data, 5, 'x'); // 'x'
```

### Mixed Key Types in Nested Access

```php
$data = [
    'users' => [
        0 => ['name' => 'John'],
        1 => ['name' => 'Jane']
    ]
];

$name = Idx::get($data, ['users', 0, 'name']);
// Result: 'John'
```

### Null Values vs. Missing Keys

```php
$data = ['key' => null];

// Key exists but value is null
$value = Idx::get($data, 'key', 'default');
// Result: null (key exists)

// Key doesn't exist
$value = Idx::get($data, 'missing', 'default');
// Result: 'default'

// Use default on empty treats null as empty
$value = Idx::get($data, 'key', 'default', true);
// Result: 'default'
```

---

## Related Utilities

- [ArrayUtil](array-util.md) - Array manipulation utilities

---

**Package:** dranzd/common-utils  
**Version:** 1.0.0  
**PHP:** 8.3+
