# 1006 - SearchUtil Core Implementation

**Status:** 🚧 In Progress  
**Priority:** Medium  
**Series:** 1000 - Core Utilities  
**Dependencies:** None

---

## Overview

SearchUtil provides basic search and filtering utilities for array data.

## Business Context

Searching and filtering data is common for:
- Filtering collections by criteria
- Finding items in arrays
- Applying custom filters
- Data processing pipelines

## Objectives

- Provide search by criteria
- Support callback-based filtering
- Ensure type safety
- Plan for advanced search features

---

## Tasks

### 1. Core Implementation

- [x] Implement `search()` method
- [x] Implement `filter()` method
- [ ] Add more search methods (findOne, findAll)
- [ ] Add sorting utilities
- [ ] Add grouping utilities

### 2. Type Safety

- [x] Add strict types
- [x] Add type hints
- [x] Add PHPDoc blocks

### 3. Documentation

- [ ] Create API documentation (docs/api/search-util.md)
- [x] Add usage examples in README

---

## Acceptance Criteria

- [x] Basic search works
- [x] Filter callback works
- [x] Type hints complete
- [ ] Extended features implemented
- [ ] API documentation complete
- [ ] Unit test coverage ≥ 80%

---

## Example Usage

```php
use Dranzd\Common\Utils\SearchUtil;

$data = [
    ['name' => 'John Doe', 'age' => 30],
    ['name' => 'Jane Smith', 'age' => 25],
    ['name' => 'Bob Johnson', 'age' => 35]
];

$results = SearchUtil::search($data, ['age' => 30]);
// Returns: [['name' => 'John Doe', 'age' => 30]]

$adults = SearchUtil::filter($data, fn($item) => $item['age'] >= 30);
// Returns: [['name' => 'John Doe', 'age' => 30], ['name' => 'Bob Johnson', 'age' => 35]]
```

---

## Related Features

- **2000 - Unit Test Coverage:** Will add comprehensive tests
- **6002 - Collection Utilities:** Future advanced collection operations

---

**Created:** November 9, 2025  
**Completed:** In Progress  
**Actual Effort:** TBD
