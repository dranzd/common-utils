# DateUtil

Strict, format-based date validation. A string is valid only if it is a real date/time written
exactly in the requested format; PHP's lenient parsing (`strtotime`, overflow, relative words) is
not used.

## isDateValid

```php
public static function isDateValid(string $date, string $format = 'Y-m-d'): bool
```

- Parses with `DateTimeImmutable::createFromFormat('!' . $format, ...)` in UTC, formats the result
  back and requires it to equal the input. That rejects overflow such as `2024-02-30` and
  non-padded input such as `2024-1-5` for `Y-m-d`. The parse-warning check is not needed
  separately: a randomised search over 400,000 format/input pairs found no input that parses with
  warnings yet formats back to itself.
- Only the round trip above is guaranteed. Fields the format does not mention come from the Unix
  epoch (1970-01-01 00:00:00 UTC), not from today: `31` is valid for `d`, `02-29` is not valid for
  `m-d`. The fixed timezone means the result never depends on the default timezone or DST.
- Years before 1901 and after 2038 are valid.
- `j` and `n` accept non-padded values and reject padded ones (`5/1/2024` is valid for `j/n/Y`,
  `05/1/2024` is not). With `D`, the day name must match the date; month and day names are case
  sensitive.
- `DATE_ATOM` rejects a `Z` suffix (it formats back as `+00:00`) and accepts `+00:00`; the `p`
  format character is the opposite: it accepts `Z` and rejects `+00:00`.
- Timezone identifiers (`e`) and abbreviations (`T`) are checked only as far as PHP's parser does:
  `Europe/Paris` and `EST` are accepted, `Foo` and `XYZ` are not, and an offset such as `+25:00` is
  accepted.
- A date containing a NUL byte is not valid.

```php
DateUtil::isDateValid('2024-02-29');                            // true
DateUtil::isDateValid('2023-02-29');                            // false
DateUtil::isDateValid('2024-02-30');                            // false
DateUtil::isDateValid('2024-1-5');                              // false
DateUtil::isDateValid('31/12/2024', 'd/m/Y');                   // true
DateUtil::isDateValid('2024-06-01T10:00:00+00:00', DATE_ATOM);  // true
DateUtil::isDateValid('2024-06-01T10:00:00Z', DATE_ATOM);       // false
DateUtil::isDateValid('02-29', 'm-d');                          // false (epoch year 1970)
DateUtil::isDateValid('2024 N', 'Y \N');                        // true (escaped, so a literal)
```

### Throws

`\InvalidArgumentException` if `$format`:

- is empty, or contains a NUL byte, or ends with a lone backslash;
- contains an unescaped character that can never validate:
  - supported by `format()` but not parsed by `createFromFormat()`: `N w W o t L B I Z c r`. The
    message suggests `DATE_ATOM` for `c` and `DATE_RFC2822` for `r`;
  - parser-only characters that `format()` writes back as literals: `? * + ! |`.

Escape a character with a backslash to use it as a literal.
