# Contributing to Common Utils

Thank you for considering contributing to `dranzd/common-utils`! This document outlines the guidelines for contributing to this project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Contribution Guidelines](#contribution-guidelines)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Documentation](#documentation)
- [Pull Request Process](#pull-request-process)

---

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inclusive environment for all contributors, regardless of experience level, gender, gender identity and expression, sexual orientation, disability, personal appearance, body size, race, ethnicity, age, religion, or nationality.

### Expected Behavior

- Be respectful and considerate
- Welcome newcomers and help them get started
- Focus on constructive feedback
- Accept criticism gracefully
- Show empathy towards other community members

### Unacceptable Behavior

- Harassment, trolling, or discriminatory comments
- Personal attacks or insults
- Publishing others' private information
- Any conduct that would be inappropriate in a professional setting

---

## Getting Started

### Prerequisites

- PHP 8.3 or higher
- Composer
- Git

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/common-utils.git
   cd common-utils
   ```

3. Add upstream remote:
   ```bash
   git remote add upstream https://github.com/dranzd/common-utils.git
   ```

---

## Development Setup

### Install Dependencies

```bash
composer install
```

### Run Tests

```bash
composer test
```

### Run Static Analysis

```bash
composer phpstan
```

### Check Code Style

```bash
composer cs-check
```

### Fix Code Style

```bash
composer cs-fix
```

### Using Docker

This project includes a `utils` script (similar to Laravel Sail) for easy Docker management:

```bash
# Start containers
./utils up

# Stop containers
./utils down

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

# Run all quality checks (test + phpstan + cs-check)
./utils quality

# View all available commands
./utils
```

**Traditional Docker Compose:**

```bash
# Build container
docker-compose build

# Run tests
docker-compose run --rm php composer test

# Interactive shell
docker-compose run --rm php bash
```

---

## Contribution Guidelines

### What to Contribute

We welcome contributions in the following areas:

1. **Bug Fixes**
   - Fix existing issues
   - Improve error handling
   - Edge case handling

2. **New Utilities**
   - Framework-agnostic utilities only
   - Must use only native PHP
   - Should solve common problems

3. **Documentation**
   - API documentation
   - Usage examples
   - Tutorials and guides

4. **Tests**
   - Unit tests for existing utilities
   - Edge case coverage
   - Integration tests

5. **Performance Improvements**
   - Optimization of existing code
   - Benchmarking

### What NOT to Contribute

- Framework-specific utilities (Laravel, Symfony, etc.)
- External dependencies (unless absolutely necessary)
- Breaking changes without discussion
- Code that doesn't follow our standards

---

## Coding Standards

### PHP Standards

- **PSR-12** coding style
- **Strict types** in all files: `declare(strict_types=1);`
- **Type hints** for all parameters and return types
- **Final classes** for utility classes
- **Static methods** for utility functions

### Example

```php
<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils;

/**
 * Brief description of the utility class.
 * 
 * Detailed description if needed.
 */
final class ExampleUtil
{
    /**
     * Brief description of the method.
     *
     * @param string $input The input parameter
     * @param bool $flag Optional flag (default: false)
     * @return string The result
     */
    public static function exampleMethod(string $input, bool $flag = false): string
    {
        // Implementation
        return $input;
    }
}
```

### Documentation Standards

- **PHPDoc blocks** for all classes and methods
- **Parameter descriptions** for all parameters
- **Return type descriptions** for all return values
- **Usage examples** in API documentation
- **Edge cases** documented

### Naming Conventions

- **Classes**: PascalCase (e.g., `StringUtil`)
- **Methods**: camelCase (e.g., `toSnakeCase`)
- **Variables**: camelCase (e.g., `$userName`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_LENGTH`)

---

## Testing

### Writing Tests

1. **Create test file** in `tests/` directory:
   ```php
   <?php
   
   declare(strict_types=1);
   
   namespace Dranzd\Common\Utils\Tests;
   
   use PHPUnit\Framework\TestCase;
   use Dranzd\Common\Utils\StringUtil;
   
   final class StringUtilTest extends TestCase
   {
       public function testToSnakeCase(): void
       {
           $result = StringUtil::toSnakeCase('firstName');
           $this->assertSame('first_name', $result);
       }
   }
   ```

2. **Test coverage requirements**:
   - All public methods must be tested
   - Edge cases must be covered
   - Error conditions must be tested

3. **Test naming**:
   - Use descriptive names: `testMethodNameWithSpecificScenario`
   - Group related tests in the same class

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
./vendor/bin/phpunit tests/StringUtilTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

---

## Documentation

### API Documentation

When adding new utilities, create API documentation in `docs/api/`:

```markdown
# UtilityName API Reference

## Overview

Brief description of the utility.

## Methods

### methodName()

Description of the method.

\`\`\`php
public static function methodName(string $param): string
\`\`\`

#### Parameters
- `string $param` - Description

#### Returns
`string` - Description

#### Examples
\`\`\`php
use Dranzd\Common\Utils\UtilityName;

$result = UtilityName::methodName('input');
// Result: 'output'
\`\`\`
```

### README Updates

Update the main README.md with:
- New utility in Features section
- Usage example in Quick Start
- API reference entry

---

## Pull Request Process

### Before Submitting

1. **Update your fork**:
   ```bash
   git fetch upstream
   git rebase upstream/main
   ```

2. **Create a feature branch**:
   ```bash
   git checkout -b feature/amazing-utility
   ```

3. **Make your changes**:
   - Write code following our standards
   - Add tests
   - Update documentation

4. **Run quality checks**:
   ```bash
   composer test
   composer phpstan
   composer cs-check
   ```

5. **Commit your changes**:
   ```bash
   git add .
   git commit -m "Add amazing utility for X"
   ```

### Commit Message Guidelines

- Use present tense: "Add feature" not "Added feature"
- Use imperative mood: "Move cursor to..." not "Moves cursor to..."
- Limit first line to 72 characters
- Reference issues: "Fix #123: Description"

### Submitting the PR

1. **Push to your fork**:
   ```bash
   git push origin feature/amazing-utility
   ```

2. **Create Pull Request** on GitHub

3. **PR Description should include**:
   - What changes were made
   - Why the changes were made
   - How to test the changes
   - Related issues (if any)

### PR Template

```markdown
## Description
Brief description of changes

## Motivation
Why is this change needed?

## Changes
- List of changes made

## Testing
How to test these changes

## Checklist
- [ ] Tests pass
- [ ] PHPStan passes
- [ ] Code style is correct
- [ ] Documentation updated
- [ ] CHANGELOG updated
```

### Review Process

1. Maintainers will review your PR
2. Address any feedback or requested changes
3. Once approved, your PR will be merged
4. Your contribution will be credited in the release notes

---

## Framework-Agnostic Principles

### Core Principle

**This library must remain framework-agnostic.**

### What This Means

✅ **Allowed:**
- Native PHP functions
- Standard PHP extensions (json, mbstring, etc.)
- Pure PHP implementations

❌ **Not Allowed:**
- Laravel helpers (Str, Arr, etc.)
- Symfony components
- Laminas/Zend components
- Any framework-specific code

### Example

```php
// ❌ BAD - Framework dependency
use Illuminate\Support\Str;

public static function toSnakeCase(string $string): string
{
    return Str::snake($string);
}

// ✅ GOOD - Native PHP
public static function toSnakeCase(string $string): string
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
}
```

---

## Questions?

- **Issues**: [GitHub Issues](https://github.com/dranzd/common-utils/issues)
- **Discussions**: [GitHub Discussions](https://github.com/dranzd/common-utils/discussions)
- **Email**: dranzd@gmail.com

---

## Recognition

Contributors will be:
- Listed in release notes
- Credited in the repository
- Mentioned in the CHANGELOG

Thank you for contributing! 🎉
