# API Documentation

Complete API reference for all utilities in the `dranzd/common-utils` library.

## Available Utilities

### Core Utilities

| Utility | Description | Documentation |
|---------|-------------|---------------|
| **ArrayUtil** | Array key case conversion and merging | [array-util.md](array-util.md) |
| **StringUtil** | String case conversion utilities | [string-util.md](string-util.md) |
| **Idx** | Safe array/object property access | [idx.md](idx.md) |
| **DateUtil** | Date validation utilities | [date-util.md](date-util.md) |
| **MoneyUtil** | Money conversion utilities | [money-util.md](money-util.md) |
| **ObjectUtil** | Object reflection and manipulation | [object-util.md](object-util.md) |
| **SearchUtil** | SQL LIKE pattern generation | [search-util.md](search-util.md) |

## Quick Links

- [Installation Guide](../../README.md#installation)
- [Quick Start Examples](../../README.md#quick-start)
- [Contributing Guidelines](../../CONTRIBUTING.md)
- [Changelog](../../CHANGELOG.md)

## Documentation Status

| Utility | Status | Coverage |
|---------|--------|----------|
| ArrayUtil | ✅ Complete | 100% |
| StringUtil | ✅ Complete | 100% |
| Idx | ✅ Complete | 100% |
| DateUtil | ⏳ Pending | 0% |
| MoneyUtil | ⏳ Pending | 0% |
| ObjectUtil | ⏳ Pending | 0% |
| SearchUtil | ⏳ Pending | 0% |

## Common Patterns

### Case Conversion Workflow

```php
// API Request (camelCase) → Database (snake_case)
$apiData = ['firstName' => 'John', 'lastName' => 'Doe'];
$dbData = ArrayUtil::keyToSnakeCase($apiData);
// Result: ['first_name' => 'John', 'last_name' => 'Doe']

// Database (snake_case) → API Response (camelCase)
$dbRow = ['first_name' => 'John', 'last_name' => 'Doe'];
$apiResponse = ArrayUtil::keyToCamelCase($dbRow);
// Result: ['firstName' => 'John', 'lastName' => 'Doe']
```

### Safe Data Access

```php
// Nested array access with defaults
$config = ['database' => ['host' => 'localhost']];
$host = Idx::get($config, ['database', 'host'], '127.0.0.1');
$port = Idx::get($config, ['database', 'port'], 3306);
```

### Money Handling

```php
// Convert float to cents (avoid floating point errors)
$amount = 19.99;
$cents = MoneyUtil::fromFloatToLowestDenomination($amount);
// Result: 1999

// Convert cents back to float
$float = MoneyUtil::fromLowestDenominationToFloat($cents);
// Result: 19.99
```

## Type Safety

All utilities use:
- `declare(strict_types=1)` for strict type checking
- Full type hints for parameters and return values
- PHPDoc annotations for complex types
- PHP 8.3+ features (mixed, nullable types)

## Performance

All utilities are optimized for:
- Minimal memory usage
- Fast execution
- No external dependencies
- Efficient algorithms

## Framework Compatibility

These utilities are **framework-agnostic** and work with:
- ✅ Laravel
- ✅ Symfony
- ✅ Laminas
- ✅ CodeIgniter
- ✅ Slim
- ✅ Any PHP 8.3+ application

## Need Help?

- 📖 [Full README](../../README.md)
- 🐛 [Report Issues](https://github.com/dranzd/common-utils/issues)
- 💬 [Discussions](https://github.com/dranzd/common-utils/discussions)
- 📧 [Email Support](mailto:dranzd@gmail.com)

---

**Package:** dranzd/common-utils  
**Version:** 1.0.0  
**PHP:** 8.3+  
**License:** MIT
