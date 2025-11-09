# 2001 - PHPStan Level 8+ Compliance

**Status:** ⏳ Planned  
**Priority:** High  
**Series:** 2000 - Testing & Quality Assurance  
**Dependencies:** 1000, 1001, 1002, 1003, 1004, 1005, 1006

---

## Overview

Ensure all code passes PHPStan static analysis at level 8 or higher to catch type errors, undefined variables, and other issues before runtime.

## Business Context

Static analysis provides:
- Early error detection
- Better IDE support
- Type safety verification
- Code quality assurance
- Reduced runtime errors

## Objectives

- Pass PHPStan level 8 analysis
- Fix all type-related issues
- Add missing type hints
- Improve PHPDoc annotations
- Maintain compliance going forward

---

## Tasks

### 1. Setup

- [x] PHPStan installed (^1.12)
- [x] Configuration file exists (phpstan.neon)
- [ ] Verify baseline configuration
- [ ] Set up CI/CD integration

### 2. Analysis

- [ ] Run PHPStan on all source files
- [ ] Document all errors
- [ ] Categorize issues
- [ ] Prioritize fixes

### 3. Fix Type Issues

- [ ] Add missing type hints
- [ ] Fix incorrect type hints
- [ ] Add generic type annotations
- [ ] Fix mixed type usage

### 4. Fix PHPDoc Issues

- [ ] Add missing @param tags
- [ ] Add missing @return tags
- [ ] Add missing @throws tags
- [ ] Fix incorrect PHPDoc types

### 5. Fix Logic Issues

- [ ] Fix undefined variable usage
- [ ] Fix unreachable code
- [ ] Fix dead code
- [ ] Fix comparison issues

### 6. Verification

- [ ] Run PHPStan level 8
- [ ] Ensure zero errors
- [ ] Add to composer scripts
- [ ] Document any exceptions

---

## Acceptance Criteria

- [ ] PHPStan level 8 passes with zero errors
- [ ] All type hints are accurate
- [ ] All PHPDoc is complete
- [ ] No suppressions used (or documented)
- [ ] CI/CD runs PHPStan

---

## Example Fixes

```php
// Before
public function get($data, $key, $default = null)
{
    return $data[$key] ?? $default;
}

// After
/**
 * @param array<string, mixed>|object $data
 * @param string|array<int, string> $key
 * @param mixed $default
 * @return mixed
 */
public function get(array|object $data, string|array $key, mixed $default = null): mixed
{
    return $data[$key] ?? $default;
}
```

---

## Related Features

- **2000 - Unit Test Coverage:** Tests help verify types
- **4000 - CI/CD Pipeline:** Automated PHPStan checks

---

**Created:** November 9, 2025  
**Completed:** Planned  
**Estimated Effort:** 4-6 hours
