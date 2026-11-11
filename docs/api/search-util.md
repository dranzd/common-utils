# SearchUtil

Builds SQL `LIKE` patterns from user-typed search text. Whitespace-separated terms are joined with
`%`, and literal `%`, `_` and the escape character typed by the user are escaped so they match
only themselves. Multibyte text is preserved.

The result is a value to bind as a query parameter, never to concatenate into SQL. Escaping only
works if the database uses the same escape character:

| Engine         | With the default backslash                       | With another character |
|----------------|--------------------------------------------------|------------------------|
| MySQL, MariaDB | default; no clause (explicit form: `ESCAPE '\\'`) | `ESCAPE '!'`           |
| PostgreSQL     | default; no clause needed                        | `ESCAPE '!'`           |
| SQLite, Oracle | no default; needs `ESCAPE '\'`                   | `ESCAPE '!'`           |

Passing a non-backslash escape character and writing `ESCAPE '!'` reads the same on every engine
and is the recommended portable form:

```sql
SELECT * FROM users WHERE name LIKE :pattern ESCAPE '!'
```

SQL Server also treats `[` as a wildcard, which this class does not escape. SQL Server is not
supported.

Input is trimmed and split on Unicode whitespace. Control characters are kept as they are.

The escape character must be exactly one valid UTF-8 character that is not `%`, `_`, whitespace or
a control character.

## escape

```php
public static function escape(string $value, string $escapeChar = '\\'): string
```

- Escapes the escape character first, then `%` and `_`.

```php
SearchUtil::escape('50%');        // 50\%
SearchUtil::escape('a_b\\c');     // a\_b\\c
SearchUtil::escape('50%', '!');   // 50!%
```

Throws `\InvalidArgumentException` if `$escapeChar` is invalid (see above) or `$value` is not valid
UTF-8.

## toLoose

```php
public static function toLoose(string $filter, string $escapeChar = '\\'): string
```

- Trims, splits on Unicode whitespace, escapes each term, joins with `%`.
- An empty or whitespace-only filter gives `''`, which as a pattern matches only the empty string.
  The three wrappers below return `%` instead.

```php
SearchUtil::toLoose('  john   smith ');     // john%smith
SearchUtil::toLoose('50% off_sale', '!');    // 50!%%off!_sale
SearchUtil::toLoose('');                     // (empty string)
```

Throws `\InvalidArgumentException` for invalid UTF-8 or an invalid `$escapeChar`.

## toLooseIncludeEnds, toLooseIncludeLeft, toLooseIncludeRight

```php
public static function toLooseIncludeEnds(string $filter, string $escapeChar = '\\'): string
public static function toLooseIncludeLeft(string $filter, string $escapeChar = '\\'): string
public static function toLooseIncludeRight(string $filter, string $escapeChar = '\\'): string
```

- Ends: `%…%` (contains). Left: `%…` (ends with). Right: `…%` (starts with).
- An empty filter gives a single `%` (match all), never `%%`.

```php
SearchUtil::toLooseIncludeEnds('john smith');  // %john%smith%
SearchUtil::toLooseIncludeLeft('john');        // %john
SearchUtil::toLooseIncludeRight('john');       // john%
SearchUtil::toLooseIncludeEnds('');            // %
```

Throws `\InvalidArgumentException` as `toLoose` does.
