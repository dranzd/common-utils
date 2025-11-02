# Changelog

All notable changes to `dranzd/common-utils` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Additional string utilities (truncate, slug, sanitize)
- Enhanced date utilities (timezone handling, relative dates)
- Collection utilities (map, filter, reduce with type safety)
- Validation utilities (email, URL, UUID)
- File path utilities (normalize, join, resolve)

## [1.0.0] - 2025-11-02

### Added
- Initial release of `dranzd/common-utils`
- **ArrayUtil** - Array key case conversion utilities
  - `keyToSnakeCase()` - Convert array keys to snake_case
  - `keyToCamelCase()` - Convert array keys to camelCase
  - `keyToKebabCase()` - Convert array keys to kebab-case
  - `merge()` - Recursive array merging with numeric key preservation
- **StringUtil** - String case conversion utilities
  - `toSnakeCase()` - Convert strings to snake_case
  - `toCamelCase()` - Convert strings to camelCase
  - `toKebabCase()` - Convert strings to kebab-case
- **Idx** - Safe property access utility
  - `get()` - Safe array/object access with defaults and nested key support
- **DateUtil** - Date validation utilities
  - `isDateValid()` - Validate date strings
- **MoneyUtil** - Money conversion utilities
  - `fromFloatToLowestDenomination()` - Convert float to integer cents
  - `fromLowestDenominationToFloat()` - Convert integer cents to float
- **ObjectUtil** - Object reflection utilities
  - `instance()` - Singleton pattern support
  - `valueOfProperty()` - Get property values via reflection
  - `setPropertyValue()` - Set property values via reflection
  - `newInstanceWithoutConstructor()` - Create instances without constructor
- **SearchUtil** - SQL LIKE pattern generation
  - `toLooseIncludeEnds()` - Generate %pattern% wildcards
  - `toLooseIncludeLeft()` - Generate %pattern wildcards
  - `toLooseIncludeRight()` - Generate pattern% wildcards
  - `toLoose()` - Convert to loose search pattern

### Changed
- Separated from `dranzd/common-event-sourcing` library
- Updated namespace from `Dranzd\Common\EventSourcing\Util` to `Dranzd\Common\Utils`
- Upgraded PHP requirement to 8.3+
- Removed all framework dependencies (Laravel, Laminas)
- Implemented native PHP alternatives for all utilities

### Technical
- Added `declare(strict_types=1)` to all files
- Improved type hints with PHP 8.3 features (mixed, nullable types)
- Added comprehensive PHPDoc documentation
- Updated dev dependencies:
  - PHPUnit 11.0
  - PHPStan 1.12
  - PHP_CodeSniffer 3.10
- Framework-agnostic implementation using only native PHP

### Documentation
- Comprehensive README with usage examples
- API documentation for all utilities
- Migration documentation from event-sourcing library
- Library separation plan

---

## Version History

### Pre-1.0.0 (Event Sourcing Library)

These utilities were originally part of `dranzd/common-event-sourcing` before being separated into their own package.

**Why the separation?**
- Better modularity and single responsibility
- Reduced dependencies for projects only needing utilities
- Framework-agnostic design
- Independent versioning and maintenance

---

## Upgrade Guide

### From dranzd/common-event-sourcing

If you were using utilities from the event-sourcing library:

1. **Install the new package:**
   ```bash
   composer require dranzd/common-utils
   ```

2. **Update namespaces:**
   ```php
   // Old
   use Dranzd\Common\EventSourcing\Util\ArrayUtil;
   use Dranzd\Common\EventSourcing\Util\StringUtil;
   
   // New
   use Dranzd\Common\Utils\ArrayUtil;
   use Dranzd\Common\Utils\StringUtil;
   ```

3. **Update PHP version:**
   - Minimum PHP version is now 8.3

4. **No API changes:**
   - All method signatures remain the same
   - Behavior is identical to previous versions

---

## Contributing

See [CONTRIBUTING.md](../../CONTRIBUTING.md) for contribution guidelines.

---

## License

MIT License - see [LICENSE](../../LICENSE) for details.
