<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use Closure;
use Dranzd\Common\Utils\ArrayUtil;
use Dranzd\Common\Utils\StringUtil;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @phpstan-type Converter Closure(array<array-key, mixed>, bool=): array<array-key, mixed>
 */
#[CoversClass(ArrayUtil::class)]
#[UsesClass(StringUtil::class)]
final class ArrayUtilTest extends TestCase
{
    /**
     * @return iterable<string, array{Converter, array<array-key, mixed>}>
     */
    public static function converterProvider(): iterable
    {
        yield 'snake' => [
            ArrayUtil::keyToSnakeCase(...),
            ['first_name' => 1, 'last_name' => 2, 'user_id' => 3, 'a_b' => 4, 'plain' => 5],
        ];
        yield 'camel' => [
            ArrayUtil::keyToCamelCase(...),
            ['firstName' => 1, 'lastName' => 2, 'userId' => 3, 'aB' => 4, 'plain' => 5],
        ];
        yield 'kebab' => [
            ArrayUtil::keyToKebabCase(...),
            ['first-name' => 1, 'last-name' => 2, 'user-id' => 3, 'a-b' => 4, 'plain' => 5],
        ];
        yield 'pascal' => [
            ArrayUtil::keyToPascalCase(...),
            ['FirstName' => 1, 'LastName' => 2, 'UserId' => 3, 'AB' => 4, 'Plain' => 5],
        ];
    }

    /**
     * @param Converter $convert
     * @param array<array-key, mixed> $expected
     */
    #[DataProvider('converterProvider')]
    public function testKeysAreConvertedInOrder(Closure $convert, array $expected): void
    {
        $input = ['first name' => 1, 'LAST_NAME' => 2, 'userID' => 3, 'a-b' => 4, 'plain' => 5];

        self::assertSame($expected, $convert($input));
    }

    /**
     * @param Converter $convert
     */
    #[DataProvider('converterProvider')]
    public function testEmptyArrayStaysEmpty(Closure $convert): void
    {
        self::assertSame([], $convert([]));
        self::assertSame([], $convert([], true));
    }

    /**
     * @param Converter $convert
     */
    #[DataProvider('converterProvider')]
    public function testIntegerKeysAreUntouched(Closure $convert): void
    {
        self::assertSame(['a', 'b', 'c'], $convert(['a', 'b', 'c']));

        self::assertSame([-5 => 'x'], $convert([-5 => 'x']));
        self::assertSame([0 => 'x'], $convert([0 => 'x']));

        $converted = array_key_first($convert(['ab' => 1]));
        $result = $convert([5 => 'x', 'ab' => 'y', 7 => 'z']);

        self::assertSame([5, $converted, 7], array_keys($result));
        self::assertSame(['x', 'y', 'z'], array_values($result));
    }

    /**
     * @param Converter $convert
     */
    #[DataProvider('converterProvider')]
    public function testNestedArraysAreOnlyConvertedWhenRecursive(Closure $convert): void
    {
        $input = ['outer key' => ['inner key' => ['deep key' => 1]]];
        $top = array_key_first($convert(['outer key' => 1]));
        $inner = array_key_first($convert(['inner key' => 1]));
        $deep = array_key_first($convert(['deep key' => 1]));

        self::assertSame([$top => ['inner key' => ['deep key' => 1]]], $convert($input));
        self::assertSame([$top => ['inner key' => ['deep key' => 1]]], $convert($input, false));
        self::assertSame([$top => [$inner => [$deep => 1]]], $convert($input, true));
    }

    public function testRecursionHandlesListsOfRecords(): void
    {
        $input = ['user_list' => [['first_name' => 'A'], ['first_name' => 'B']], 'total_count' => 2];

        self::assertSame(
            ['userList' => [['firstName' => 'A'], ['firstName' => 'B']], 'totalCount' => 2],
            ArrayUtil::keyToCamelCase($input, true),
        );
    }

    public function testNonArrayValuesAreReturnedAsIs(): void
    {
        $object = new stdClass();
        $object->some_property = 1;
        $input = ['an_object' => $object, 'a_null' => null, 'a_string' => 'some_value', 'an_int' => 1];

        $result = ArrayUtil::keyToCamelCase($input, true);

        self::assertSame(['anObject', 'aNull', 'aString', 'anInt'], array_keys($result));
        self::assertSame($object, $result['anObject']);
        self::assertSame(['some_property' => 1], get_object_vars($object));
        self::assertNull($result['aNull']);
        self::assertSame('some_value', $result['aString']);
    }

    /**
     * @return iterable<string, array{array<array-key, mixed>}>
     */
    public static function nestedInputProvider(): iterable
    {
        yield 'nested' => [['outer_key' => ['inner_key' => 1]]];
        yield 'three levels' => [['a_b' => ['c_d' => ['e_f' => ['g_h' => 1]]]]];
        yield 'list of records' => [['rows_list' => [['first_name' => 'A'], ['first_name' => 'B']]]];
        yield 'mixed keys and values' => [[3 => ['x_y' => 1], 'a_b' => [1, 2], 'c_d' => 'text', 'e_f' => null]];
    }

    /**
     * @param array<array-key, mixed> $input
     */
    #[DataProvider('nestedInputProvider')]
    public function testInputIsNotMutated(array $input): void
    {
        $copy = $input;

        ArrayUtil::keyToCamelCase($input, true);

        self::assertSame($copy, $input);
    }

    public function testConvertedNumericKeyBecomesInteger(): void
    {
        self::assertSame([1 => 'a', 'b' => 'c'], ArrayUtil::keyToSnakeCase(['1_' => 'a', 'b' => 'c']));
    }

    /**
     * @return iterable<string, array{array<array-key, mixed>, bool, string}>
     */
    public static function collisionProvider(): iterable
    {
        yield 'camel and snake' => [
            ['firstName' => 1, 'first_name' => 2],
            false,
            'Keys "firstName" and "first_name" both convert to "first_name".',
        ];
        yield 'null value still collides' => [
            ['firstName' => null, 'first_name' => 2],
            false,
            'Keys "firstName" and "first_name" both convert to "first_name".',
        ];
        yield 'converted key hits existing key' => [
            ['first_name' => 1, 'firstName' => 2],
            false,
            'Keys "first_name" and "firstName" both convert to "first_name".',
        ];
        yield 'three-way' => [
            ['a b' => 1, 'a-b' => 2, 'a_b' => 3], false, 'Keys "a b" and "a-b" both convert to "a_b".',
        ];
        yield 'nested' => [
            ['outer' => ['firstName' => 1, 'first_name' => 2]], true, 'Keys "firstName" and "first_name" both convert',
        ];
        yield 'deeply nested' => [
            ['a' => ['b' => ['xY' => 1, 'x_y' => 2]]], true, 'Keys "xY" and "x_y" both convert to "x_y".',
        ];
        yield 'numeric string hits integer key' => [
            [1 => 'a', '1_' => 'b'], false, 'Keys "1" and "1_" both convert to "1".',
        ];
    }

    /**
     * @param array<array-key, mixed> $input
     */
    #[DataProvider('collisionProvider')]
    public function testCollisionsThrow(array $input, bool $recursive, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        ArrayUtil::keyToSnakeCase($input, $recursive);
    }

    public function testCollisionInNestedArrayIsIgnoredWhenNotRecursive(): void
    {
        $input = ['outer_key' => ['firstName' => 1, 'first_name' => 2]];

        self::assertSame(['outerKey' => ['firstName' => 1, 'first_name' => 2]], ArrayUtil::keyToCamelCase($input));
    }

    public function testInvalidUtf8KeyIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot split string into words: ');

        ArrayUtil::keyToSnakeCase(["bad\xFFkey" => 1]);
    }

    /**
     * @return iterable<string, array{array<array-key, mixed>, array<array-key, mixed>, bool, array<array-key, mixed>}>
     */
    public static function mergeProvider(): iterable
    {
        yield 'both empty' => [[], [], false, []];
        yield 'empty a' => [[], ['x' => 1], false, ['x' => 1]];
        yield 'empty b' => [['x' => 1], [], false, ['x' => 1]];
        yield 'b wins on scalars' => [['x' => 1, 'y' => 2], ['y' => 3], false, ['x' => 1, 'y' => 3]];
        yield 'key only in b is added' => [['x' => 1], ['y' => 2], false, ['x' => 1, 'y' => 2]];
        yield 'null in b overwrites' => [['x' => 1], ['x' => null], false, ['x' => null]];
        yield 'array in b replaces scalar' => [['x' => 1], ['x' => [1]], false, ['x' => [1]]];
        yield 'scalar in b replaces array' => [['x' => [1]], ['x' => 1], false, ['x' => 1]];
        yield 'lists concatenate' => [['a' => [1, 2]], ['a' => [3]], false, ['a' => [1, 2, 3]]];
        yield 'top-level lists concatenate' => [[1, 2], [3], false, [1, 2, 3]];
        yield 'list keys are renumbered' => [[5 => 'a'], [9 => 'b'], false, [5 => 'a', 6 => 'b']];
        yield 'nested maps merge' => [
            ['a' => ['b' => ['c' => 1, 'd' => 2]]],
            ['a' => ['b' => ['d' => 3, 'e' => 4]], 'f' => 5],
            false,
            ['a' => ['b' => ['c' => 1, 'd' => 3, 'e' => 4]], 'f' => 5],
        ];
        yield 'nested lists inside list are appended whole' => [
            [['a' => 1]],
            [['b' => 2]],
            false,
            [['a' => 1], ['b' => 2]],
        ];
        yield 'mixed keys' => [
            ['x' => 1, 10, 20],
            ['x' => 2, 30],
            false,
            ['x' => 2, 10, 20, 30],
        ];
        yield 'array value under a key only in b' => [['x' => 1], ['y' => [1]], false, ['x' => 1, 'y' => [1]]];
        yield 'numeric-string keys are integer keys' => [['10' => 'a'], ['10' => 'b'], false, [10 => 'a', 11 => 'b']];
        yield 'preserve: numeric-string keys overwrite' => [['10' => 'a'], ['10' => 'b'], true, [10 => 'b']];
        yield 'preserve: integer key overwritten' => [[1, 2], [3], true, [3, 2]];
        yield 'preserve: integer key added' => [[1], [2 => 'c'], true, [1, 2 => 'c']];
        yield 'preserve: nested lists overwrite by index' => [
            ['a' => [1, 2]],
            ['a' => [3]],
            true,
            ['a' => [3, 2]],
        ];
        yield 'preserve: integer keys merge recursively' => [
            [['a' => 1, 'b' => 2]],
            [['b' => 3, 'c' => 4]],
            true,
            [['a' => 1, 'b' => 3, 'c' => 4]],
        ];
        yield 'preserve: integer scalar replaces array' => [[[1]], [2], true, [2]];
        yield 'preserve: deep mixed' => [
            ['a' => ['b' => [1, 2, 3], 'c' => 'x'], 'd' => [1]],
            ['a' => ['b' => [9], 'c' => null], 'd' => 5],
            true,
            ['a' => ['b' => [9, 2, 3], 'c' => null], 'd' => 5],
        ];
        yield 'append: deep mixed' => [
            ['a' => ['b' => [1, 2, 3], 'c' => 'x'], 'd' => [1]],
            ['a' => ['b' => [9], 'c' => null], 'd' => 5],
            false,
            ['a' => ['b' => [1, 2, 3, 9], 'c' => null], 'd' => 5],
        ];
    }

    /**
     * @param array<array-key, mixed> $a
     * @param array<array-key, mixed> $b
     * @param array<array-key, mixed> $expected
     */
    #[DataProvider('mergeProvider')]
    public function testMerge(array $a, array $b, bool $preserveNumericKeys, array $expected): void
    {
        self::assertSame($expected, ArrayUtil::merge($a, $b, $preserveNumericKeys));
    }

    public function testMergePreservesNumericKeysOnlyOnRequest(): void
    {
        self::assertSame([1, 2], ArrayUtil::merge([1], [2]));
        self::assertSame([['a' => 1], ['b' => 2]], ArrayUtil::merge([['a' => 1]], [['b' => 2]]));
        self::assertSame(['k' => [1, 2]], ArrayUtil::merge(['k' => [1]], ['k' => [2]]));
    }

    public function testMergeRejectsAppendingBeyondTheLargestIntegerKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('largest integer key');

        ArrayUtil::merge([PHP_INT_MAX => 'a'], ['b']);
    }

    /**
     * @param array<array-key, mixed> $a
     * @param array<array-key, mixed> $b
     */
    #[DataProvider('mergeProvider')]
    public function testMergeDoesNotMutateItsArguments(array $a, array $b, bool $preserveNumericKeys): void
    {
        $aCopy = $a;
        $bCopy = $b;

        ArrayUtil::merge($a, $b, $preserveNumericKeys);

        self::assertSame($aCopy, $a);
        self::assertSame($bCopy, $b);
    }
}
