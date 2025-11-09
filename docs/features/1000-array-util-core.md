# 1000 - ArrayUtil Core Implementation

**Status:** ✅ Completed  
**Priority:** Critical  
**Series:** 1000 - Core Utilities  
**Dependencies:** None

---

## Overview

ArrayUtil provides framework-agnostic array manipulation utilities, focusing on key case conversion (snake_case, camelCase, kebab-case) and array merging operations.

## Business Context

Modern PHP applications often need to transform data between different naming conventions:
- API responses (camelCase) to database columns (snake_case)
- Frontend data (kebab-case) to backend data (snake_case)
- Normalizing user input from various sources

## Objectives

- Provide reliable array key case conversion utilities
- Support recursive transformation of nested arrays
- Implement framework-agnostic array merging
- Maintain immutability (don't modify input data)
- Ensure type safety with PHP 8.3 features

---

## Tasks

### 1. Core Implementation

- [x] Implement `keyToSnakeCase()` method
- [x] Implement `keyToCamelCase()` method
- [x] Implement `keyToKebabCase()` method
- [x] Support recursive conversion

### 2. Array Merging

- [x] Implement `merge()` method
- [x] Implement `mergePreservingNumericKeys()` helper

### 3. Framework Independence

- [x] Remove Laravel dependency
- [x] Remove Laminas dependency
- [x] Implement native PHP alternatives

### 4. Type Safety

- [x] Add strict types
- [x] Add type hints
- [x] Add PHPDoc blocks

---

## Acceptance Criteria

- [x] Framework-agnostic implementation
- [x] Recursive conversion works
- [x] Type hints complete
- [x] PSR-12 compliant
- [x] API documentation complete
- [ ] Unit test coverage ≥ 80%

---

## Example Usage

```php
use Dranzd\Common\Utils\ArrayUtil;

$data = ['firstName' => 'John', 'lastName' => 'Doe'];
$snakeCase = ArrayUtil::keyToSnakeCase($data);
// Result: ['first_name' => 'John', 'last_name' => 'Doe']
```

---

## Related Features

- **1001 - StringUtil Core:** Uses string conversion methods
- **2000 - Unit Test Coverage:** Will add comprehensive tests

---

**Created:** November 9, 2025  
**Completed:** November 2, 2025  
**Actual Effort:** 2 hours
