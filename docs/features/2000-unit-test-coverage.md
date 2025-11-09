# 2000 - Unit Test Coverage for All Utilities

**Status:** ⏳ Planned  
**Priority:** Critical  
**Series:** 2000 - Testing & Quality Assurance  
**Dependencies:** 1000, 1001, 1002, 1003, 1004, 1005, 1006

---

## Overview

Implement comprehensive unit test coverage for all utility classes to ensure reliability, catch regressions, and maintain code quality.

## Business Context

Unit tests are essential for:
- Ensuring code correctness
- Preventing regressions
- Documenting expected behavior
- Enabling safe refactoring
- Building confidence in the library

## Objectives

- Achieve ≥80% code coverage for all utilities
- Test all public methods
- Cover edge cases and error conditions
- Use PHPUnit 11.0+
- Follow testing best practices

---

## Tasks

### 1. Test Infrastructure

- [ ] Verify PHPUnit configuration
- [ ] Set up test directory structure
- [ ] Configure code coverage reporting
- [ ] Set up test helpers if needed

### 2. ArrayUtil Tests

- [ ] Test `keyToSnakeCase()` - basic cases
- [ ] Test `keyToSnakeCase()` - recursive
- [ ] Test `keyToSnakeCase()` - edge cases
- [ ] Test `keyToCamelCase()` - all cases
- [ ] Test `keyToKebabCase()` - all cases
- [ ] Test `merge()` - basic and recursive
- [ ] Test numeric key preservation

### 3. StringUtil Tests

- [ ] Test `toSnakeCase()` - various inputs
- [ ] Test `toCamelCase()` - various inputs
- [ ] Test `toKebabCase()` - various inputs
- [ ] Test edge cases (numbers, special chars)

### 4. Idx Tests

- [ ] Test array access - basic
- [ ] Test array access - nested
- [ ] Test object access
- [ ] Test default values
- [ ] Test empty value handling
- [ ] Test edge cases

### 5. DateUtil Tests

- [ ] Test `format()` method
- [ ] Test `parse()` method
- [ ] Test `addDays()` method
- [ ] Test `subDays()` method
- [ ] Test edge cases

### 6. MoneyUtil Tests

- [ ] Test `format()` method
- [ ] Test `sum()` method
- [ ] Test `addPercentage()` method
- [ ] Test precision handling
- [ ] Test edge cases

### 7. ObjectUtil Tests

- [ ] Test `toArray()` method
- [ ] Test `deepClone()` method
- [ ] Test `getProperty()` method
- [ ] Test `setProperty()` method
- [ ] Test with various object types

### 8. SearchUtil Tests

- [ ] Test `search()` method
- [ ] Test `filter()` method
- [ ] Test with various data types
- [ ] Test edge cases

### 9. Coverage Analysis

- [ ] Run coverage report
- [ ] Identify gaps
- [ ] Add tests for uncovered code
- [ ] Achieve ≥80% coverage

---

## Acceptance Criteria

- [ ] All utilities have test files
- [ ] Code coverage ≥ 80%
- [ ] All tests pass
- [ ] Edge cases covered
- [ ] Test documentation complete

---

## Example Test

```php
<?php

namespace Dranzd\Common\Utils\Tests;

use PHPUnit\Framework\TestCase;
use Dranzd\Common\Utils\ArrayUtil;

class ArrayUtilTest extends TestCase
{
    public function testKeyToSnakeCase(): void
    {
        $input = ['firstName' => 'John', 'lastName' => 'Doe'];
        $expected = ['first_name' => 'John', 'last_name' => 'Doe'];
        
        $result = ArrayUtil::keyToSnakeCase($input);
        
        $this->assertEquals($expected, $result);
    }
    
    public function testKeyToSnakeCaseRecursive(): void
    {
        $input = [
            'userInfo' => [
                'firstName' => 'John',
                'emailAddress' => 'john@example.com'
            ]
        ];
        
        $expected = [
            'user_info' => [
                'first_name' => 'John',
                'email_address' => 'john@example.com'
            ]
        ];
        
        $result = ArrayUtil::keyToSnakeCase($input, true);
        
        $this->assertEquals($expected, $result);
    }
}
```

---

## Related Features

- **1000-1006:** All core utilities being tested
- **2002 - PHPStan Compliance:** Static analysis
- **4002 - Coverage Reporting:** CI/CD integration

---

**Created:** November 9, 2025  
**Completed:** Planned  
**Estimated Effort:** 8-12 hours
