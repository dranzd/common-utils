# StringUtil

Converts strings between snake_case, camelCase, PascalCase and kebab-case. All four methods share one
tokenizer, so any input style can be converted to any output style. The tokenizer splits on:

- runs of characters that are not letters, digits or combining marks (space, `_`, `-`, `.`, ...);
  leading and trailing separators are ignored;
- case boundaries: lower case or digit followed by upper case (`firstName`), and an acronym followed
  by a capitalised word (`HTTPServer` gives `http`, `server`).

Digits stay attached to the preceding word (`userID2Name` gives `user`, `id2`, `name`). Matching is
Unicode-aware, and an empty string converts to an empty string.

## toSnakeCase

```php
public static function toSnakeCase(string $string): string
```

```php
StringUtil::toSnakeCase('Email-Address'); // 'email_address'
StringUtil::toSnakeCase('HTTPServer');    // 'http_server'
StringUtil::toSnakeCase('userID2Name');   // 'user_id2_name'
StringUtil::toSnakeCase('élanVital');     // 'élan_vital'
```

## toCamelCase

```php
public static function toCamelCase(string $string): string
```

```php
StringUtil::toCamelCase('LAST_NAME');  // 'lastName'
StringUtil::toCamelCase('first name'); // 'firstName'
```

## toPascalCase

```php
public static function toPascalCase(string $string): string
```

```php
StringUtil::toPascalCase('first_name'); // 'FirstName'
```

## toKebabCase

```php
public static function toKebabCase(string $string): string
```

```php
StringUtil::toKebabCase('first name'); // 'first-name'
StringUtil::toKebabCase('FirstName');  // 'first-name'
```

## Notes and limits

- Characters without case (for example CJK) are never split or changed.
- The first letter of each word is title-cased, not upper-cased: `toPascalCase('ßeta')` is `Sseta` (not
  `SSeta`) and `toPascalCase('ǆemal')` is `ǅemal` (not `Ǆemal`).
- Separators are dropped wherever they occur, so meaningful prefixes are lost: `_id` and `$ref` convert
  like `id` and `ref`, and `__typename` like `typename`.
- Acronyms are normalised: `toCamelCase('userID')` is `userId` and `toCamelCase('iOS')` is `iOs`.
- A lower-case plural after an acronym splits oddly: `toSnakeCase('MyURLs')` is `my_ur_ls`.
- A zero-width joiner counts as a separator, so `toSnakeCase("a\u{200D}b")` is `a_b`.
- Applying the same conversion twice gives the same result as applying it once, with these exceptions:
  - Single-letter words are lost by PascalCase and by camelCase after the first word.
    `toPascalCase('a_b')` is `AB`, which is read back as the single word `ab`.
  - A word whose first letter has no single upper-case form merges with the previous word in
    PascalCase and camelCase. `toCamelCase('x_ǆy')` is `xǅy`; converting that again gives `xǆy`,
    because `ǅ` is title case and is not treated as a case boundary.

## Throws

Every method throws `\InvalidArgumentException` when the string is not valid UTF-8.
