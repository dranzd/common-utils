# MoneyUtil

Converts between a decimal major-unit amount (for example `19.99`) and an integer minor-unit
amount (`1999`). It is a boundary helper, for example for parsing user or API input. Money should
be stored and calculated as integer minor units, never as floats.

The denomination is the number of minor units per major unit: `100` for USD (the default), `1`
for JPY, `1000` for KWD.

## MAX_SAFE_MINOR_UNITS

```php
public const MAX_SAFE_MINOR_UNITS = 562949953421312; // 2 ** 49
```

The largest absolute amount, in minor units, that converts exactly. A double has 53 bits, and the
scaling multiplication and PHP 8.3's `round()` (which pre-rounds to 15 significant digits) both
cost precision. Randomised comparison of decimal-string inputs against exact integer arithmetic,
for denominations 1 to 10000, found no mismatch up to this bound on PHP 8.3, and mismatches begin
near 1e15 (for example `10000126640353.45` gave `1000012664035344`). The bound is a power of two
with a safety margin: the worst-case float error there is well under half a minor unit.

## fromFloatToLowestDenomination

```php
public static function fromFloatToLowestDenomination(float $floatValue, int $lowestDenomination = 100): int
```

- Computes `(int) round($floatValue * $lowestDenomination)`; halves round away from zero.
- Absorbs typical float noise such as `0.1 + 0.2`.
- The input is a binary float, so values like `1.005` are not exactly representable: the result
  depends on the float, and differs between PHP 8.3 and 8.4+. Pass strings through a decimal
  parser if you need exactness.

```php
MoneyUtil::fromFloatToLowestDenomination(19.99);         // 1999
MoneyUtil::fromFloatToLowestDenomination(0.1 + 0.2);     // 30
MoneyUtil::fromFloatToLowestDenomination(-19.99);        // -1999
MoneyUtil::fromFloatToLowestDenomination(1234.0, 1);     // 1234 (JPY)
MoneyUtil::fromFloatToLowestDenomination(1.234, 1000);   // 1234 (KWD)
```

Throws `\InvalidArgumentException` if the denomination is below 1, the value is NAN or infinite,
or the scaled amount exceeds `MAX_SAFE_MINOR_UNITS` ("out of range").

## fromLowestDenominationToFloat

```php
public static function fromLowestDenominationToFloat(int $integerValue, int $lowestDenomination = 100): float
```

- Returns `$integerValue / $lowestDenomination`. Intended for display or output at the boundary.
- Converting an integer amount to float and back with the same denomination returns the same
  integer, provided the amount is within +/- `MAX_SAFE_MINOR_UNITS`. Beyond it, no guarantee is made
  (and converting back throws).

```php
MoneyUtil::fromLowestDenominationToFloat(1999);        // 19.99
MoneyUtil::fromLowestDenominationToFloat(500, 1);      // 500.0
MoneyUtil::fromLowestDenominationToFloat(1234, 1000);  // 1.234
```

Throws `\InvalidArgumentException` if the denomination is below 1.
