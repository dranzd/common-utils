# Common Utils

[![CI](https://github.com/dranzd/common-utils/actions/workflows/ci.yml/badge.svg)](https://github.com/dranzd/common-utils/actions/workflows/ci.yml)
[![Latest version](https://img.shields.io/packagist/v/dranzd/common-utils.svg)](https://packagist.org/packages/dranzd/common-utils)
[![PHP version](https://img.shields.io/packagist/php-v/dranzd/common-utils.svg)](composer.json)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A small set of dependency-free PHP utilities for the chores that keep turning up at the edges of
an application: converting key casing between an API and a database, reading values out of nested
data without `isset()` chains, validating dates strictly, and building safe SQL `LIKE` patterns.

Seven `final` classes of stateless static helpers. No framework, no runtime dependencies beyond
`ext-mbstring`, checked by PHPStan at its maximum level.

| Class | Purpose |
|---|---|
| [`StringUtil`](docs/api/string-util.md) | Convert between snake_case, camelCase, PascalCase and kebab-case (Unicode-aware) |
| [`ArrayUtil`](docs/api/array-util.md) | Convert array key casing, recursively; merge nested arrays |
| [`Idx`](docs/api/idx.md) | Read a value from nested arrays and objects, with a default |
| [`ObjectUtil`](docs/api/object-util.md) | Read and write properties of any visibility; instantiate without a constructor |
| [`DateUtil`](docs/api/date-util.md) | Strict, format-based date validation |
| [`MoneyUtil`](docs/api/money-util.md) | Convert between decimal amounts and integer minor units |
| [`SearchUtil`](docs/api/search-util.md) | Build escaped SQL `LIKE` patterns from user input |

## Installation

```bash
composer require dranzd/common-utils
```

Requires PHP 8.3 or newer and `ext-mbstring`.

## Usage

All classes live in the `Dranzd\Common\Utils` namespace. Invalid arguments throw
`\InvalidArgumentException`.

### StringUtil

Every conversion accepts any input style, because all of them share one word tokenizer.

```php
StringUtil::toSnakeCase('HTTPServerError'); // 'http_server_error'
StringUtil::toCamelCase('LAST_NAME');       // 'lastName'
StringUtil::toPascalCase('user-id');        // 'UserId'
StringUtil::toKebabCase('Élan Vital');      // 'élan-vital'
```

### ArrayUtil

```php
ArrayUtil::keyToSnakeCase([
    'userId' => 7,
    'contactDetails' => ['emailAddress' => 'a@example.com'],
    'tags' => ['a', 'b'],
], recursive: true);
// ['user_id' => 7, 'contact_details' => ['email_address' => 'a@example.com'], 'tags' => ['a', 'b']]
```

`keyToCamelCase()`, `keyToPascalCase()` and `keyToKebabCase()` work the same way. Integer keys are
left alone, and two keys that would convert to the same key throw instead of silently dropping data:

```php
ArrayUtil::keyToSnakeCase(['firstName' => 1, 'first_name' => 2]); // throws InvalidArgumentException
```

`merge()` merges nested arrays. String keys are merged recursively; lists are concatenated unless
you ask for integer keys to be preserved:

```php
$defaults  = ['debug' => false, 'db' => ['host' => 'localhost', 'replicas' => ['a']]];
$overrides = ['debug' => true, 'db' => ['port' => 5432, 'replicas' => ['b']]];

ArrayUtil::merge($defaults, $overrides);
// ['debug' => true, 'db' => ['host' => 'localhost', 'replicas' => ['a', 'b'], 'port' => 5432]]

ArrayUtil::merge([1, 2], [3], preserveNumericKeys: true); // [3, 2]
```

### Idx

`Idx::get()` walks a key or a path through arrays, `ArrayAccess` objects and public properties. For a
non-public property it uses the matching accessor (`getX()`, `isX()`, `hasX()`, `x()`) when there is
one, and reads the property directly otherwise. It never calls a method that has no property behind it.

```php
$order = [
    'id' => 42,
    'customer' => (object) ['name' => 'Ada', 'email' => null],
    'total' => new Money(1999, 'USD'),   // private $amount; public function currency()
    'lines' => new ArrayObject([['sku' => 'A-1']]),
];

Idx::get($order, ['customer', 'name']);                // 'Ada'
Idx::get($order, ['customer', 'phone'], 'n/a');        // 'n/a'  (missing)
Idx::get($order, ['customer', 'email'], 'n/a');        // null   (present, and null)
Idx::get($order, ['customer', 'email'], 'n/a', true);  // 'n/a'  (default on empty)
Idx::get($order, ['total', 'currency']);               // 'USD'  (accessor method)
Idx::get($order, ['total', 'amount']);                 // 1999   (private property)
Idx::get($order, ['lines', 0, 'sku']);                 // 'A-1'
Idx::get($order, ['lines', 3, 'sku'], '-');            // '-'
```

The exact lookup order, and what counts as "empty", is in the [Idx reference](docs/api/idx.md).

### ObjectUtil

Reflection helpers for infrastructure code such as hydrators and tests. Private properties declared
on parent classes are found too.

```php
$money = ObjectUtil::newInstanceWithoutConstructor(Money::class);
ObjectUtil::setPropertyValue($money, 'amount', 500);

ObjectUtil::getPropertyValue($money, 'amount'); // 500
ObjectUtil::hasProperty($money, 'nope');        // false
```

### DateUtil

A string is valid only if it is a real calendar date written exactly in the given format.

```php
DateUtil::isDateValid('2024-02-29');          // true
DateUtil::isDateValid('2024-02-30');          // false (no such day)
DateUtil::isDateValid('tomorrow');            // false (not in the format)
DateUtil::isDateValid('31/12/2024', 'd/m/Y'); // true
```

### MoneyUtil

For the boundary between decimal input and integer storage. Store and calculate money as integer
minor units; floats are only safe to convert once, on the way in or out.

```php
MoneyUtil::fromFloatToLowestDenomination(19.99);     // 1999
MoneyUtil::fromFloatToLowestDenomination(0.1 + 0.2); // 30
MoneyUtil::fromFloatToLowestDenomination(1500.0, 1); // 1500 (currency without minor units)
MoneyUtil::fromLowestDenominationToFloat(1999);      // 19.99
```

### SearchUtil

Turns what a user typed into a `LIKE` pattern, escaping the wildcards they did not mean.

```php
SearchUtil::toLooseIncludeEnds('  john   smith '); // '%john%smith%'
SearchUtil::toLooseIncludeRight('50% off');        // '50\%%off%'
SearchUtil::toLooseIncludeEnds('');                // '%'
```

The result is a value to bind, never SQL to concatenate. Backslash is the default escape character,
which MySQL, MariaDB and PostgreSQL understand without an `ESCAPE` clause. For a query that reads the
same on every engine, pass your own escape character and declare it:

```php
$statement = $pdo->prepare("SELECT * FROM users WHERE name LIKE :pattern ESCAPE '!'");
$statement->execute(['pattern' => SearchUtil::toLooseIncludeEnds($input, '!')]);
```

SQL Server is not supported, because it also treats `[` as a wildcard.

## Design notes

- **Stateless.** No I/O and no global state. Only `ObjectUtil::setPropertyValue()` modifies what
  you pass in.
- **Fail loudly.** Bad input is an `\InvalidArgumentException` with a message naming the problem.
- **Unicode-aware.** String handling uses `mb_*` functions and `/u` regular expressions.
- **No runtime dependencies.** Development tooling is the only thing Composer installs.

## Development

```bash
composer install
composer check        # coding standards, static analysis and tests
```

| Command | Tool |
|---|---|
| `composer test` | PHPUnit |
| `composer test:coverage` | PHPUnit with coverage (needs pcov or Xdebug) |
| `composer phpstan` | PHPStan, max level with strict rules |
| `composer cs-check` / `composer cs-fix` | PHP_CodeSniffer, PSR-12 |

A Docker setup is included for running the same commands without a local PHP: `make install`, then
`make check`.

CI runs the suite on PHP 8.3, 8.4 and 8.5 and requires at least 95% line coverage.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Security issues: see [SECURITY.md](SECURITY.md).

## License

[MIT](LICENSE)
