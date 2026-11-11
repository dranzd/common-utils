# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-11-11

First public release. The earlier `1.0.0` tag was a private pre-release; the notes below describe
what changed compared with it.

### Added

- `StringUtil::toPascalCase()` and `ArrayUtil::keyToPascalCase()`.
- `ObjectUtil::hasProperty()`.
- `SearchUtil::escape()`, and an `$escapeChar` parameter on every `SearchUtil` method.
- A `$format` parameter on `DateUtil::isDateValid()` (default `Y-m-d`).
- `MoneyUtil::MAX_SAFE_MINOR_UNITS`, the largest amount that converts exactly.
- A full test suite, PHPStan at max level with strict rules, and CI on PHP 8.3, 8.4 and 8.5.

### Changed

Invalid arguments now throw `InvalidArgumentException` throughout. Where the previous code let an
engine error escape (`ReflectionException`, `Error`, `TypeError`, `DivisionByZeroError`), callers
that caught those types must catch `InvalidArgumentException` instead.

#### StringUtil

- All conversions share one Unicode-aware tokenizer, so any input style converts to any output
  style: `toCamelCase('LAST_NAME')` is `lastName` (was `lASTNAME`) and
  `toSnakeCase('Email-Address')` is `email_address` (was `email-address`).
- Every character that is not a letter or digit separates words, and leading, trailing and repeated
  separators are dropped: `_id` becomes `id`, `__typename` becomes `typename`, `$ref` becomes `ref`.
- Acronyms are normalised in input that is already camelCase: `toCamelCase('userID')` is `userId`
  (was `userID`), and `iOS` becomes `iOs`.
- Non-ASCII upper-case letters are lower-cased (`ÉlanVital` becomes `élan_vital`).
- A string that is not valid UTF-8 throws; it used to pass through.

#### ArrayUtil

- `keyTo*Case()`: keys that would collide after conversion throw instead of silently overwriting
  each other. Because of the tokenizer changes above, `['_id' => 1, 'id' => 2]` now collides. A
  converted key that is numeric (`'1_'`) becomes an integer key.
- `merge()`: by default, values under integer keys are now appended, so lists concatenate; this
  used to behave like `array_replace_recursive()`. Pass `$preserveNumericKeys = true` to overwrite
  by integer key as before. With `true`, two arrays at the same integer key are now merged
  recursively (the second used to replace the first).

#### Idx

- `Idx::get()` now also reads `ArrayAccess` offsets, magic `__isset()`/`__get()` properties,
  `isX()`/`hasX()` accessors, integer keys on objects, and private properties inherited from parent
  classes.
- An `ArrayAccess` object is asked for the offset first; its properties are consulted only when the
  offset is missing.
- `$useDefaultOnEmpty` treats only `null`, `''`, `[]` and empty `Countable` objects as empty; `0`,
  `'0'` and `false` are now returned. An empty accessor result no longer falls through to the next
  accessor or to the raw property.
- `$key` must be an int, a string or a list of those: `null` now raises a `TypeError`, and an
  invalid path segment throws.
- The first parameter is named `$subject` (was `$object`), which affects named-argument calls.
- Static properties are no longer read.

#### ObjectUtil

- Methods are static (they can still be called on `ObjectUtil::instance()`).
- Property lookup walks the whole class hierarchy, so private properties of any ancestor are
  reachable. A missing property throws on write instead of being ignored, and on read throws
  `InvalidArgumentException` instead of `ReflectionException`. Reading an uninitialized property and
  rejected writes (readonly, incompatible type) throw `InvalidArgumentException` as well.
- Static properties are not instance state and are no longer read or written.
- `newInstanceWithoutConstructor()` always throws `InvalidArgumentException` when it cannot create
  an instance. It used to return `null` for an unknown name and let engine errors escape otherwise;
  the return type is `object` rather than `?object`.

#### DateUtil

- `isDateValid()` validates strictly against a format (default `Y-m-d`) instead of asking
  `strtotime()`. Anything not written exactly in that format is now invalid, including impossible
  dates (`2024-02-30`), relative expressions (`tomorrow`, `now`), date-times
  (`2024-01-05 10:00:00`), other layouts (`01/05/2024`, `20240105`) and unpadded or padded variants
  (`2024-1-5`, `' 2024-01-05'`). Dates before 1901 are now valid.
- A format that could never validate throws instead of returning `false`.

#### MoneyUtil

- A denomination below 1, a non-finite amount, or an amount beyond `MAX_SAFE_MINOR_UNITS` throws.
  Large amounts used to be converted with silent rounding errors.

#### SearchUtil

- `%`, `_` and the escape character in user input are escaped. Patterns can therefore contain the
  escape character (backslash by default), which SQLite and Oracle only honour with an `ESCAPE`
  clause; see the [SearchUtil reference](docs/api/search-util.md).
- Input is trimmed and split on Unicode whitespace, so multibyte text is no longer corrupted and
  `toLooseIncludeRight(' john')` is `john%` (was `%john%`). Control characters are no longer turned
  into `%`.
- An empty filter yields `%` rather than `%%` from the three `toLooseInclude*()` methods, and `''`
  from `toLoose()` (was `%` for whitespace-only input).
- Input that is not valid UTF-8 throws.

#### Requirements

- Requires `ext-mbstring`; `ext-json` is no longer required.

### Deprecated

- `ObjectUtil::instance()` and `ObjectUtil::valueOfProperty()` are kept so existing callers keep
  working, and will be removed in 2.0. Every method is static now; call
  `ObjectUtil::getPropertyValue()` and the others directly.

### Removed

- The optional trailing `ReflectionClass` parameters on `ObjectUtil` methods. Positional calls that
  still pass one keep working, because PHP ignores the extra argument; calls that pass it by name
  (`reflection:`) do not.

[Unreleased]: https://github.com/dranzd/common-utils/compare/v1.1.0...master
[1.1.0]: https://github.com/dranzd/common-utils/releases/tag/v1.1.0
