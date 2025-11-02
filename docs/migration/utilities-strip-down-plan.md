# Utilities Library Strip-Down Plan

## Overview

This document outlines the process of stripping down the `dranzd/common-event-sourcing` repository to create a focused `dranzd/common-utils` library containing only framework-agnostic utility functions. This library will serve as a foundational dependency for other libraries in the ecosystem.

## Motivation

The utilities in the original event-sourcing library are:
- **Framework-agnostic** - Pure PHP utility functions
- **Reusable across projects** - Not specific to event sourcing
- **Foundational** - Can be used by other separated libraries
- **Lightweight** - Minimal dependencies required

By separating these utilities, we create a clean, focused library that can be:
- Used independently in any PHP project
- Composed with other libraries without pulling unnecessary dependencies
- Maintained and versioned separately
- Tested in isolation

## Current State Analysis

### Utilities to Keep

Located in `src/Util/`:
- **ArrayUtil.php** - Array key case conversion utilities (snake_case, camelCase, kebab-case)
- **DateUtil.php** - Date manipulation and formatting utilities
- **Idx.php** - Safe array/object property access with defaults
- **MoneyUtil.php** - Money calculation utilities
- **ObjectUtil.php** - Object manipulation and reflection utilities
- **SearchUtil.php** - Search and filtering utilities
- **StringUtil.php** - String case conversion utilities

### Components to Remove

All event-sourcing specific components:
- `src/Domain/` - All domain-specific code (Events, Aggregates, ValueObjects, Messages, etc.)
- Event sourcing abstractions
- CQRS infrastructure
- Domain events and aggregates
- Value objects
- Assertions (will be moved to separate library)

### Dependencies Analysis

**Current Dependencies:**
```json
"require": {
    "php": "^8.2",
    "ext-json": "*",
    "ramsey/uuid": "^4.7",
    "webmozart/assert": "^1.11"
}
```

**Framework Dependencies Found:**
- `Illuminate\Support\Str` (Laravel) - Used in StringUtil
- `Laminas\Stdlib\ArrayUtils` (Laminas) - Used in ArrayUtil

**Target Dependencies (Framework-Agnostic):**
```json
"require": {
    "php": "^8.3",
    "ext-json": "*"
}
```

## Execution Plan

### ✅ Step 1: Create Utilities Strip-Down Documentation
**Status:** In Progress  
**Estimated Time:** 1 hour

**Tasks:**
- [x] Analyze current repository structure
- [x] Identify utilities to keep
- [x] Identify components to remove
- [x] Document framework dependencies to eliminate
- [x] Create this strip-down plan

**Deliverables:**
- `docs/migration/utilities-strip-down-plan.md`

---

### ✅ Step 2: Remove Framework Dependencies
**Status:** Pending  
**Estimated Time:** 2-3 hours

**Tasks:**
- [ ] Replace `Illuminate\Support\Str` with native PHP implementations
  - `Str::snake()` → Custom implementation
  - `Str::camel()` → Custom implementation
  - `Str::kebab()` → Custom implementation
- [ ] Replace `Laminas\Stdlib\ArrayUtils::merge()` with native PHP implementation
- [ ] Remove all framework dependencies from composer.json
- [ ] Update all utility classes to use only native PHP functions
- [ ] Ensure PHP 8.3 compatibility

**Files to Update:**
```
src/Util/StringUtil.php
src/Util/ArrayUtil.php
```

**Implementation Notes:**
```php
// Replace Str::snake() with:
function toSnakeCase(string $string): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
}

// Replace Str::camel() with:
function toCamelCase(string $string): string
{
    return lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $string))));
}

// Replace Str::kebab() with:
function toKebabCase(string $string): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
}

// Replace ArrayUtils::merge() with native array_merge_recursive or custom implementation
```

**Deliverables:**
- Framework-agnostic utility implementations
- Updated composer.json without framework dependencies

---

### ✅ Step 3: Remove Event Sourcing Components
**Status:** Pending  
**Estimated Time:** 1 hour

**Tasks:**
- [ ] Delete `src/Domain/` directory entirely
- [ ] Delete event sourcing tests from `tests/Domain/`
- [ ] Keep only utility tests in `tests/Util/`
- [ ] Update namespace references if needed
- [ ] Clean up any unused imports

**Directories to Remove:**
```
src/Domain/
tests/Domain/
```

**Directories to Keep:**
```
src/Util/
tests/Util/
```

**Deliverables:**
- Clean repository with only utility code
- Passing utility tests

---

### ✅ Step 4: Update Package Configuration
**Status:** Pending  
**Estimated Time:** 1-2 hours

**Tasks:**
- [ ] Update composer.json
  - Change package name to `dranzd/common-utils`
  - Update description
  - Update PHP version to `^8.3`
  - Update dependencies to latest versions
  - Update namespace to `Dranzd\Common\Utils`
  - Remove unnecessary dev dependencies
- [ ] Update phpunit.xml configuration
- [ ] Update phpstan configuration
- [ ] Update .gitignore if needed
- [ ] Update LICENSE file
- [ ] Verify Docker configuration

**New composer.json Structure:**
```json
{
    "name": "dranzd/common-utils",
    "description": "Framework-agnostic utility functions for PHP applications",
    "type": "library",
    "license": "MIT",
    "keywords": ["utilities", "helpers", "array", "string", "date", "money", "framework-agnostic"],
    "authors": [
        {
            "name": "Dranzd",
            "email": "dranzd@gmail.com"
        }
    ],
    "minimum-stability": "stable",
    "require": {
        "php": "^8.3",
        "ext-json": "*"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0",
        "phpstan/phpstan": "^1.12",
        "squizlabs/php_codesniffer": "^3.10"
    },
    "autoload": {
        "psr-4": {
            "Dranzd\\Common\\Utils\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Dranzd\\Common\\Utils\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit",
        "phpstan": "phpstan analyse src tests --level=8",
        "cs-check": "phpcs --standard=PSR12 src tests",
        "cs-fix": "phpcbf --standard=PSR12 src tests"
    },
    "config": {
        "sort-packages": true,
        "optimize-autoloader": true
    }
}
```

**Deliverables:**
- Updated composer.json with correct package information
- Latest dependency versions
- PHP 8.3 requirement

---

### ✅ Step 5: Update Namespaces
**Status:** Pending  
**Estimated Time:** 1-2 hours

**Tasks:**
- [ ] Update all utility class namespaces from `Dranzd\Common\EventSourcing\Util` to `Dranzd\Common\Utils`
- [ ] Update all test class namespaces
- [ ] Update any cross-references between utility classes
- [ ] Run composer dump-autoload
- [ ] Verify all tests still pass

**Files to Update:**
```
src/Util/ArrayUtil.php
src/Util/DateUtil.php
src/Util/Idx.php
src/Util/MoneyUtil.php
src/Util/ObjectUtil.php
src/Util/SearchUtil.php
src/Util/StringUtil.php
tests/Util/* (all test files)
```

**Namespace Change:**
```php
// Old:
namespace Dranzd\Common\EventSourcing\Util;

// New:
namespace Dranzd\Common\Utils;
```

**Deliverables:**
- All files using new namespace
- Autoloader working correctly
- All tests passing

---

### ✅ Step 6: Create New Documentation
**Status:** Pending  
**Estimated Time:** 2-3 hours

**Tasks:**
- [ ] Create new README.md for utilities library
  - Installation instructions
  - Feature overview
  - Usage examples for each utility
  - API reference
  - Requirements (PHP 8.3+)
- [ ] Remove event-sourcing specific documentation
- [ ] Keep only migration documentation
- [ ] Create CHANGELOG.md
- [ ] Create CONTRIBUTING.md
- [ ] Add usage examples for each utility class

**Documentation Structure:**
```
docs/
├── migration/
│   ├── utilities-strip-down-plan.md (this file)
│   └── library-separation-plan.md (reference)
└── api/
    ├── array-util.md
    ├── string-util.md
    ├── idx.md
    ├── date-util.md
    ├── money-util.md
    ├── object-util.md
    └── search-util.md
```

**README.md Sections:**
- Overview
- Features
- Requirements (PHP 8.3+)
- Installation
- Quick Start
- API Reference
  - ArrayUtil
  - StringUtil
  - Idx
  - DateUtil
  - MoneyUtil
  - ObjectUtil
  - SearchUtil
- Development
- Testing
- Contributing
- License

**Deliverables:**
- Comprehensive README.md
- API documentation for each utility
- CHANGELOG.md
- CONTRIBUTING.md

---

### ✅ Step 7: Enhance Utilities with PHP 8.3 Features
**Status:** Pending  
**Estimated Time:** 2-3 hours

**Tasks:**
- [ ] Review all utility classes for PHP 8.3 improvements
- [ ] Add typed class constants where applicable
- [ ] Use readonly properties where appropriate
- [ ] Add return type declarations for all methods
- [ ] Use strict types in all files
- [ ] Consider using new PHP 8.3 features:
  - Typed class constants
  - Dynamic class constant fetch
  - json_validate() function
  - Override attribute
- [ ] Add comprehensive PHPDoc blocks
- [ ] Ensure PHPStan level 8+ compliance

**PHP 8.3 Improvements Example:**
```php
<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

final readonly class StringUtil
{
    public const string DEFAULT_ENCODING = 'UTF-8';
    
    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
```

**Deliverables:**
- Modern PHP 8.3 code
- Improved type safety
- Better IDE support

---

### ✅ Step 8: Comprehensive Testing
**Status:** Pending  
**Estimated Time:** 2-3 hours

**Tasks:**
- [ ] Review existing utility tests
- [ ] Add missing test coverage
- [ ] Ensure 100% code coverage for critical paths
- [ ] Add edge case tests
- [ ] Add PHP 8.3 specific tests
- [ ] Run full test suite
- [ ] Run PHPStan analysis
- [ ] Run code style checks
- [ ] Fix any issues found

**Test Coverage Goals:**
- ArrayUtil: 100%
- StringUtil: 100%
- Idx: 100%
- DateUtil: 100%
- MoneyUtil: 100%
- ObjectUtil: 100%
- SearchUtil: 100%

**Deliverables:**
- Comprehensive test suite
- 100% coverage on critical paths
- All tests passing
- PHPStan level 8+ passing
- PSR-12 compliance

---

### ✅ Step 9: CI/CD Setup
**Status:** Pending  
**Estimated Time:** 1-2 hours

**Tasks:**
- [ ] Create/update GitHub Actions workflow
  - PHP 8.3 testing
  - Multiple OS testing (Ubuntu, macOS, Windows)
  - PHPUnit execution
  - PHPStan analysis
  - Code style checks
  - Coverage reporting
- [ ] Add badges to README.md
  - Build status
  - Coverage
  - PHPStan level
  - PHP version
  - License
- [ ] Set up automated releases

**GitHub Actions Workflow:**
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ${{ matrix.os }}
    strategy:
      matrix:
        os: [ubuntu-latest, macos-latest, windows-latest]
        php: ['8.3']
    
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          coverage: xdebug
      
      - run: composer install
      - run: composer test
      - run: composer phpstan
      - run: composer cs-check
```

**Deliverables:**
- Working CI/CD pipeline
- Automated testing on multiple platforms
- README badges

---

### ✅ Step 10: Final Verification and Release
**Status:** Pending  
**Estimated Time:** 1-2 hours

**Tasks:**
- [ ] Final code review
- [ ] Verify all documentation is accurate
- [ ] Test installation in fresh project
- [ ] Verify Packagist compatibility
- [ ] Create initial git tag (v1.0.0)
- [ ] Publish to Packagist (or private repository)
- [ ] Update library-separation-plan.md with completion status
- [ ] Archive old event-sourcing documentation

**Verification Checklist:**
- [ ] Package name is `dranzd/common-utils`
- [ ] PHP 8.3+ requirement
- [ ] No framework dependencies
- [ ] All utilities are framework-agnostic
- [ ] All tests pass
- [ ] PHPStan level 8+ passes
- [ ] Code style compliant
- [ ] Documentation complete
- [ ] CI/CD working
- [ ] Can be installed via Composer

**Deliverables:**
- Published `dranzd/common-utils` v1.0.0
- Complete documentation
- Working CI/CD
- Ready for use in other libraries

---

## Dependency Graph After Separation

```
dranzd/common-utils (no dependencies)
    ↑
    ├── dranzd/common-assert (depends on: common-utils)
    ├── dranzd/common-valueobject (depends on: common-utils, common-assert)
    ├── dranzd/common-messaging (depends on: common-utils, common-valueobject)
    └── dranzd/common-event-sourcing (depends on: messaging, valueobject, assert, utils)
```

## Framework-Agnostic Principles

### What Makes a Utility Framework-Agnostic?

1. **No Framework Dependencies**
   - No Laravel, Symfony, Laminas, etc.
   - Only native PHP and standard extensions

2. **Pure Functions**
   - Stateless operations
   - Predictable outputs
   - No side effects

3. **Standard PHP Only**
   - Uses only built-in PHP functions
   - Standard library extensions (json, mbstring, etc.)
   - No external packages unless absolutely necessary

4. **Universal Applicability**
   - Can be used in any PHP project
   - Not tied to specific architecture
   - No assumptions about application structure

### Implementation Guidelines

```php
// ✅ Good - Framework-agnostic
final class StringUtil
{
    public static function toSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}

// ❌ Bad - Framework-dependent
final class StringUtil
{
    public static function toSnakeCase(string $string): string
    {
        return Str::snake($string); // Laravel dependency
    }
}
```

## Risk Assessment

### High Risk
- **Breaking existing dependencies** - Mitigated by creating new package name
- **Framework dependency removal** - Requires careful reimplementation

### Medium Risk
- **Test coverage gaps** - Addressed with comprehensive testing
- **PHP 8.3 compatibility** - Thorough testing on PHP 8.3

### Low Risk
- **Namespace changes** - Straightforward find/replace
- **Documentation updates** - Clear structure defined

## Success Criteria

- ✅ Package name is `dranzd/common-utils`
- ✅ PHP 8.3+ requirement
- ✅ Zero framework dependencies
- ✅ All utilities are framework-agnostic
- ✅ 100% test coverage on critical paths
- ✅ PHPStan level 8+ passes
- ✅ PSR-12 code style compliant
- ✅ Comprehensive documentation
- ✅ Working CI/CD pipeline
- ✅ Published to Packagist
- ✅ Can be used as dependency in other libraries

## Timeline Estimate

**Total Estimated Time:** 15-22 hours

- **Phase 1: Cleanup** (Steps 1-3) - 4-5 hours
- **Phase 2: Configuration** (Steps 4-5) - 2-4 hours
- **Phase 3: Documentation** (Step 6) - 2-3 hours
- **Phase 4: Enhancement** (Steps 7-8) - 4-6 hours
- **Phase 5: Release** (Steps 9-10) - 2-4 hours

## Notes

- This is a foundational library - keep it minimal and focused
- Every utility should be thoroughly tested
- Documentation is critical for adoption
- Framework-agnostic is non-negotiable
- PHP 8.3 features should be leveraged where beneficial
- Keep dependencies minimal (ideally zero external packages)

---

**Document Version:** 1.0  
**Created:** 2025-11-02  
**Last Updated:** 2025-11-02  
**Status:** Planning Phase
