# StringUtil API Reference

## Overview

`StringUtil` provides framework-agnostic string case conversion utilities using native PHP implementations.

**Namespace:** `Dranzd\Common\Utils\StringUtil`

## Methods

### toSnakeCase()

Convert a string to snake_case format.

```php
public static function toSnakeCase(string $string): string
```

#### Parameters

- `string $string` - The input string

#### Returns

`string` - The string in snake_case format

#### Examples

```php
use Dranzd\Common\Utils\StringUtil;

// Convert camelCase
$result = StringUtil::toSnakeCase('firstName');
// Result: 'first_name'

// Convert PascalCase
$result = StringUtil::toSnakeCase('FirstName');
// Result: 'first_name'

// Convert with multiple words
$result = StringUtil::toSnakeCase('userEmailAddress');
// Result: 'user_email_address'

// Already snake_case (no change)
$result = StringUtil::toSnakeCase('first_name');
// Result: 'first_name'

// Handle acronyms
$result = StringUtil::toSnakeCase('HTTPSConnection');
// Result: 'https_connection'
```

#### Algorithm

1. Checks if string is already lowercase (optimization)
2. Inserts underscore before uppercase letters following lowercase/digits
3. Handles consecutive uppercase letters (acronyms)
4. Converts entire string to lowercase

#### Use Cases

- Converting property names for database columns
- Normalizing API parameter names
- Generating file names from class names
- Creating URL slugs (combined with sanitization)

---

### toCamelCase()

Convert a string to camelCase format.

```php
public static function toCamelCase(string $string): string
```

#### Parameters

- `string $string` - The input string

#### Returns

`string` - The string in camelCase format

#### Examples

```php
use Dranzd\Common\Utils\StringUtil;

// Convert snake_case
$result = StringUtil::toCamelCase('first_name');
// Result: 'firstName'

// Convert kebab-case
$result = StringUtil::toCamelCase('first-name');
// Result: 'firstName'

// Convert with multiple words
$result = StringUtil::toCamelCase('user_email_address');
// Result: 'userEmailAddress'

// Mixed separators
$result = StringUtil::toCamelCase('user_email-address');
// Result: 'userEmailAddress'

// Already camelCase (ensures first letter is lowercase)
$result = StringUtil::toCamelCase('FirstName');
// Result: 'firstName'
```

#### Algorithm

1. Replaces underscores and hyphens with spaces
2. Capitalizes first letter of each word
3. Removes spaces
4. Ensures first character is lowercase

#### Use Cases

- Converting database column names to object properties
- Normalizing API response keys
- Generating method names from configuration
- JavaScript/JSON data transformation

---

### toKebabCase()

Convert a string to kebab-case format.

```php
public static function toKebabCase(string $string): string
```

#### Parameters

- `string $string` - The input string

#### Returns

`string` - The string in kebab-case format

#### Examples

```php
use Dranzd\Common\Utils\StringUtil;

// Convert camelCase
$result = StringUtil::toKebabCase('firstName');
// Result: 'first-name'

// Convert PascalCase
$result = StringUtil::toKebabCase('FirstName');
// Result: 'first-name'

// Convert snake_case
$result = StringUtil::toKebabCase('first_name');
// Result: 'first-name'

// Convert with multiple words
$result = StringUtil::toKebabCase('userEmailAddress');
// Result: 'user-email-address'

// Already kebab-case (no change)
$result = StringUtil::toKebabCase('first-name');
// Result: 'first-name'

// Handle acronyms
$result = StringUtil::toKebabCase('HTTPSConnection');
// Result: 'https-connection'
```

#### Algorithm

1. Checks if string is already lowercase without underscores (optimization)
2. Inserts hyphen before uppercase letters following lowercase/digits
3. Handles consecutive uppercase letters (acronyms)
4. Replaces underscores with hyphens
5. Converts entire string to lowercase

#### Use Cases

- Generating CSS class names
- Creating URL slugs
- HTML attribute names
- File naming conventions
- Vue/React component prop names

---

## Implementation Details

### Framework Independence

All methods use only native PHP functions:
- `preg_replace()` - Pattern matching and replacement
- `strtolower()` - Case conversion
- `str_replace()` - String replacement
- `ucwords()` - Word capitalization
- `lcfirst()` - Lowercase first character
- `ctype_lower()` - Character type checking
- `str_contains()` - String search (PHP 8.0+)

### Performance

- Optimized with early returns for already-converted strings
- Uses efficient regex patterns
- No external dependencies or function calls

### Type Safety

All methods use strict types (`declare(strict_types=1)`) and require PHP 8.3+.

---

## Common Patterns

### Database to API

```php
// Convert database column names to API response
$dbRow = [
    'user_id' => 1,
    'first_name' => 'John',
    'email_address' => 'john@example.com'
];

$apiResponse = array_map(
    fn($key) => StringUtil::toCamelCase($key),
    array_keys($dbRow)
);
// Keys: ['userId', 'firstName', 'emailAddress']
```

### API to Database

```php
// Convert API request to database format
$apiRequest = [
    'userId' => 1,
    'firstName' => 'John',
    'emailAddress' => 'john@example.com'
];

$dbData = array_combine(
    array_map(fn($key) => StringUtil::toSnakeCase($key), array_keys($apiRequest)),
    array_values($apiRequest)
);
// Keys: ['user_id', 'first_name', 'email_address']
```

### CSS Class Generation

```php
// Generate CSS class from component name
$componentName = 'UserProfileCard';
$cssClass = StringUtil::toKebabCase($componentName);
// Result: 'user-profile-card'
```

---

## Edge Cases

### Empty Strings

```php
StringUtil::toSnakeCase('');    // Returns: ''
StringUtil::toCamelCase('');    // Returns: ''
StringUtil::toKebabCase('');    // Returns: ''
```

### Single Character

```php
StringUtil::toSnakeCase('A');   // Returns: 'a'
StringUtil::toCamelCase('a');   // Returns: 'a'
StringUtil::toKebabCase('A');   // Returns: 'a'
```

### Numbers

```php
StringUtil::toSnakeCase('user123Name');  // Returns: 'user123_name'
StringUtil::toCamelCase('user_123_name'); // Returns: 'user123Name'
StringUtil::toKebabCase('user123Name');  // Returns: 'user123-name'
```

---

## Related Utilities

- [ArrayUtil](array-util.md) - Uses StringUtil for key conversion

---

**Package:** dranzd/common-utils  
**Version:** 1.0.0  
**PHP:** 8.3+
