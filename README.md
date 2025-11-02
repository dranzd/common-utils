# Common Utils

A collection of framework-agnostic utility functions for PHP 8.3+ applications. This library provides essential utilities for array manipulation, string conversion, safe property access, date handling, money calculations, object manipulation, and search operations.

## Features

- **ArrayUtil**: Array key case conversion (snake_case, camelCase, kebab-case) and merging
- **StringUtil**: String case conversion utilities
- **Idx**: Safe array/object property access with default values
- **DateUtil**: Date manipulation and formatting utilities
- **MoneyUtil**: Money calculation and formatting utilities
- **ObjectUtil**: Object manipulation and reflection utilities
- **SearchUtil**: Search and filtering utilities
- **Framework Agnostic**: Zero framework dependencies - pure PHP only
- **PHP 8.3+**: Modern PHP with full type safety
- **Lightweight**: Minimal dependencies (only ext-json)
- **Type Safe**: Strict types and comprehensive PHPDoc annotations

## Requirements

- PHP 8.3 or higher
- ext-json

## Installation

```bash
composer require dranzd/common-utils
```

No additional dependencies required!

## Quick Start

### ArrayUtil - Array Key Case Conversion

```php
use Dranzd\Common\Utils\ArrayUtil;

$data = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'emailAddress' => 'john@example.com'
];

// Convert to snake_case
$snakeCase = ArrayUtil::keyToSnakeCase($data);
// Result: ['first_name' => 'John', 'last_name' => 'Doe', 'email_address' => 'john@example.com']

// Convert to camelCase
$camelCase = ArrayUtil::keyToCamelCase($data);
// Result: ['firstName' => 'John', 'lastName' => 'Doe', 'emailAddress' => 'john@example.com']

// Convert to kebab-case
$kebabCase = ArrayUtil::keyToKebabCase($data);
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
$result = ArrayUtil::keyToSnakeCase($nested, true);
// Converts all nested keys recursively
```

### StringUtil - String Case Conversion

```php
use Dranzd\Common\Utils\StringUtil;

// Convert to snake_case
$snake = StringUtil::toSnakeCase('firstName');
// Result: 'first_name'

// Convert to camelCase
$camel = StringUtil::toCamelCase('first_name');
// Result: 'firstName'

// Convert to kebab-case
$kebab = StringUtil::toKebabCase('firstName');
// Result: 'first-name'
```

### Idx - Safe Property Access

```php
use Dranzd\Common\Utils\Idx;

$data = [
    'user' => [
        'name' => 'John',
        'email' => 'john@example.com'
    ]
];

// Safe access with default value
$name = Idx::get($data, ['user', 'name'], 'Unknown');
// Result: 'John'

$phone = Idx::get($data, ['user', 'phone'], 'N/A');
// Result: 'N/A' (default value)

// Works with objects too
$user = new stdClass();
$user->name = 'Jane';

$name = Idx::get($user, 'name', 'Unknown');
// Result: 'Jane'

// Use default on empty values
$empty = ['value' => ''];
$result = Idx::get($empty, 'value', 'default', true);
// Result: 'default' (because value is empty)
```

### DateUtil - Date Manipulation

```php
use Dranzd\Common\Utils\DateUtil;

// Format dates
$formatted = DateUtil::format(new DateTime(), 'Y-m-d');

// Parse dates
$date = DateUtil::parse('2024-01-15', 'Y-m-d');

// Date calculations
$tomorrow = DateUtil::addDays(new DateTime(), 1);
$lastWeek = DateUtil::subDays(new DateTime(), 7);
```

### MoneyUtil - Money Calculations

```php
use Dranzd\Common\Utils\MoneyUtil;

// Format money
$formatted = MoneyUtil::format(1234.56, 'USD');
// Result: '$1,234.56'

// Calculate totals
$total = MoneyUtil::sum([100.50, 200.75, 50.25]);
// Result: 351.50

// Apply percentage
$withTax = MoneyUtil::addPercentage(100.00, 15);
// Result: 115.00
```

### ObjectUtil - Object Manipulation

```php
use Dranzd\Common\Utils\ObjectUtil;

// Convert object to array
$array = ObjectUtil::toArray($object);

// Clone object deeply
$clone = ObjectUtil::deepClone($object);

// Get property value via reflection
$value = ObjectUtil::getProperty($object, 'privateProperty');

// Set property value via reflection
ObjectUtil::setProperty($object, 'privateProperty', 'new value');
```

### SearchUtil - Search and Filtering

```php
use Dranzd\Common\Utils\SearchUtil;

$data = [
    ['name' => 'John Doe', 'age' => 30],
    ['name' => 'Jane Smith', 'age' => 25],
    ['name' => 'Bob Johnson', 'age' => 35]
];

// Search by criteria
$results = SearchUtil::search($data, ['age' => 30]);
// Returns: [['name' => 'John Doe', 'age' => 30]]

// Filter by callback
$adults = SearchUtil::filter($data, fn($item) => $item['age'] >= 30);
// Returns: [['name' => 'John Doe', 'age' => 30], ['name' => 'Bob Johnson', 'age' => 35]]
```

## API Reference

### ArrayUtil

| Method | Description | Parameters |
|--------|-------------|------------|
| `keyToSnakeCase(array $array, bool $recursive = false)` | Convert array keys to snake_case | `$array` - Input array<br>`$recursive` - Convert nested arrays |
| `keyToCamelCase(array $array, bool $recursive = false)` | Convert array keys to camelCase | `$array` - Input array<br>`$recursive` - Convert nested arrays |
| `keyToKebabCase(array $array, bool $recursive = false)` | Convert array keys to kebab-case | `$array` - Input array<br>`$recursive` - Convert nested arrays |
| `merge(array $a, array $b, bool $preserveNumericKeys = false)` | Merge two arrays | `$a` - First array<br>`$b` - Second array<br>`$preserveNumericKeys` - Preserve numeric keys |

### StringUtil

| Method | Description | Parameters |
|--------|-------------|------------|
| `toSnakeCase(string $string)` | Convert string to snake_case | `$string` - Input string |
| `toCamelCase(string $string)` | Convert string to camelCase | `$string` - Input string |
| `toKebabCase(string $string)` | Convert string to kebab-case | `$string` - Input string |

### Idx

| Method | Description | Parameters |
|--------|-------------|------------|
| `get(mixed $object, mixed $key, mixed $default = null, bool $useDefaultOnEmpty = false)` | Safely get value from array or object | `$object` - Array or object<br>`$key` - String, numeric, or array of keys<br>`$default` - Default value if not found<br>`$useDefaultOnEmpty` - Use default if value is empty |

### DateUtil

| Method | Description | Parameters |
|--------|-------------|------------|
| `format(DateTime $date, string $format)` | Format date | `$date` - DateTime object<br>`$format` - Format string |
| `parse(string $date, string $format)` | Parse date string | `$date` - Date string<br>`$format` - Format string |
| `addDays(DateTime $date, int $days)` | Add days to date | `$date` - DateTime object<br>`$days` - Number of days |
| `subDays(DateTime $date, int $days)` | Subtract days from date | `$date` - DateTime object<br>`$days` - Number of days |

### MoneyUtil

| Method | Description | Parameters |
|--------|-------------|------------|
| `format(float $amount, string $currency)` | Format money | `$amount` - Amount<br>`$currency` - Currency code |
| `sum(array $amounts)` | Sum array of amounts | `$amounts` - Array of numbers |
| `addPercentage(float $amount, float $percentage)` | Add percentage to amount | `$amount` - Base amount<br>`$percentage` - Percentage to add |

### ObjectUtil

| Method | Description | Parameters |
|--------|-------------|------------|
| `toArray(object $object)` | Convert object to array | `$object` - Object to convert |
| `deepClone(object $object)` | Deep clone object | `$object` - Object to clone |
| `getProperty(object $object, string $property)` | Get property via reflection | `$object` - Object<br>`$property` - Property name |
| `setProperty(object $object, string $property, mixed $value)` | Set property via reflection | `$object` - Object<br>`$property` - Property name<br>`$value` - New value |

### SearchUtil

| Method | Description | Parameters |
|--------|-------------|------------|
| `search(array $data, array $criteria)` | Search by criteria | `$data` - Array to search<br>`$criteria` - Search criteria |
| `filter(array $data, callable $callback)` | Filter by callback | `$data` - Array to filter<br>`$callback` - Filter function |

## Development

### Running Tests

```bash
composer test
```

### Static Analysis

Run PHPStan at level 8:

```bash
composer phpstan
```

### Code Style

```bash
# Check code style (PSR-12)
composer cs-check

# Fix code style automatically
composer cs-fix
```

### Using Docker

This project includes a `utils` script (similar to Laravel Sail) for easy Docker management:

```bash
# Start containers
./utils up

# Stop containers
./utils down

# Restart containers
./utils restart

# Open shell
./utils shell

# Run tests
./utils test

# Run PHPStan
./utils phpstan

# Check code style
./utils cs-check

# Fix code style
./utils cs-fix

# Run all quality checks
./utils quality

# Composer commands
./utils composer install
./utils install          # shortcut
./utils composer update
./utils dump-autoload

# View all available commands
./utils
```

**Traditional Docker Compose:**

```bash
# Build the container
docker-compose build

# Run tests in container
docker-compose run --rm php composer test

# Interactive shell
docker-compose run --rm php bash
```

## Design Principles

- **Framework Agnostic**: Zero dependencies on frameworks (Laravel, Symfony, etc.)
- **Pure PHP**: Uses only native PHP functions and standard extensions
- **Type Safety**: Full PHP 8.3 type hints for better IDE support
- **Immutability**: Utility functions don't modify input data
- **Predictability**: Pure functions with consistent behavior
- **Testability**: Easy to unit test without mocking
- **Performance**: Optimized for common use cases
- **PSR-12 Compliant**: Follows PHP coding standards

## Use Cases

### API Response Transformation

```php
// Convert API response keys from camelCase to snake_case
$apiResponse = [
    'userId' => 123,
    'firstName' => 'John',
    'emailAddress' => 'john@example.com'
];

$dbFormat = ArrayUtil::keyToSnakeCase($apiResponse);
// Save to database with snake_case columns
```

### Safe Configuration Access

```php
// Safely access nested configuration
$config = [
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
];

$host = Idx::get($config, ['database', 'host'], '127.0.0.1');
$timeout = Idx::get($config, ['database', 'timeout'], 30);
```

### Data Normalization

```php
// Normalize user input
$userInput = [
    'FirstName' => 'John',
    'LAST_NAME' => 'Doe',
    'Email-Address' => 'john@example.com'
];

$normalized = ArrayUtil::keyToSnakeCase($userInput);
// All keys now in consistent snake_case format
```

## Why Framework-Agnostic?

This library is intentionally framework-agnostic to:

1. **Universal Compatibility**: Use in any PHP project (Laravel, Symfony, Laminas, vanilla PHP)
2. **No Vendor Lock-in**: Not tied to any specific framework's lifecycle
3. **Minimal Dependencies**: Only requires PHP 8.3+ and ext-json
4. **Long-term Stability**: Not affected by framework version changes
5. **Composability**: Can be used as a foundation for other libraries
6. **Performance**: No framework overhead

## Documentation

### API Reference

- **[Complete API Documentation](docs/api/README.md)** - Comprehensive API reference for all utilities
- **[ArrayUtil API](docs/api/array-util.md)** - Array manipulation utilities
- **[StringUtil API](docs/api/string-util.md)** - String conversion utilities
- **[Idx API](docs/api/idx.md)** - Safe property access

### Migration Guides

- **[Utilities Strip-Down Plan](docs/migration/utilities-strip-down-plan.md)** - Migration from event-sourcing library
- **[Library Separation Plan](docs/migration/library-separation-plan.md)** - Overall separation strategy

## Roadmap

### Planned Features

- [ ] Additional string utilities (truncate, slug, sanitize)
- [ ] Enhanced date utilities (timezone handling, relative dates)
- [ ] Collection utilities (map, filter, reduce with type safety)
- [ ] Validation utilities (email, URL, UUID)
- [ ] File path utilities (normalize, join, resolve)

### Future Considerations

- [ ] PHP 8.4 compatibility
- [ ] Performance benchmarks
- [ ] Extended documentation with more examples
- [ ] Video tutorials

## License

MIT License - see [LICENSE](LICENSE) file for details

## Contributing

Contributions are welcome! Please ensure:

1. **Tests Pass**: Run `composer test` before submitting
2. **Code Style**: Follow PSR-12 standards (`composer cs-check`)
3. **Static Analysis**: Pass PHPStan level 8 (`composer phpstan`)
4. **Documentation**: Update README and add examples
5. **Type Safety**: Use strict types and full type hints
6. **No Dependencies**: Keep the library framework-agnostic

### Contribution Guidelines

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-utility`)
3. Write tests for your changes
4. Ensure all tests pass and code style is correct
5. Commit your changes (`git commit -m 'Add amazing utility'`)
6. Push to the branch (`git push origin feature/amazing-utility`)
7. Open a Pull Request

## Support

- **Issues**: [GitHub Issues](https://github.com/dranzd/common-utils/issues)
- **Discussions**: [GitHub Discussions](https://github.com/dranzd/common-utils/discussions)
- **Email**: dranzd@gmail.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

---

**Made with ❤️ by Dranzd**
