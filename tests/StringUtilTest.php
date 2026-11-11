<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use Closure;
use Dranzd\Common\Utils\StringUtil;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(StringUtil::class)]
final class StringUtilTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string, string, string}>
     *     input, snake, camel, pascal, kebab
     */
    public static function conversionProvider(): iterable
    {
        yield 'empty string' => ['', '', '', '', ''];
        yield 'only separators' => [' _-. ', '', '', '', ''];
        yield 'single lower word' => ['name', 'name', 'name', 'Name', 'name'];
        yield 'snake input' => ['first_name', 'first_name', 'firstName', 'FirstName', 'first-name'];
        yield 'screaming snake' => ['LAST_NAME', 'last_name', 'lastName', 'LastName', 'last-name'];
        yield 'kebab input' => ['first-name', 'first_name', 'firstName', 'FirstName', 'first-name'];
        yield 'camel input' => ['firstName', 'first_name', 'firstName', 'FirstName', 'first-name'];
        yield 'pascal input' => ['FirstName', 'first_name', 'firstName', 'FirstName', 'first-name'];
        yield 'spaces' => ['first name', 'first_name', 'firstName', 'FirstName', 'first-name'];
        yield 'hyphenated' => ['Email-Address', 'email_address', 'emailAddress', 'EmailAddress', 'email-address'];
        yield 'dots and runs' => ['a..b__c', 'a_b_c', 'aBC', 'ABC', 'a-b-c'];
        yield 'edge separators' => ['__first-name--', 'first_name', 'firstName', 'FirstName', 'first-name'];
        yield 'acronym then word' => ['HTTPServer', 'http_server', 'httpServer', 'HttpServer', 'http-server'];
        yield 'trailing acronym' => ['userID', 'user_id', 'userId', 'UserId', 'user-id'];
        yield 'digits stay attached' => ['address2', 'address2', 'address2', 'Address2', 'address2'];
        yield 'digits before upper' => ['userID2Name', 'user_id2_name', 'userId2Name', 'UserId2Name', 'user-id2-name'];
        yield 'word starting with digit' => ['a_1b', 'a_1b', 'a1b', 'A1b', 'a-1b'];
        yield 'unicode lower' => ['élanVital', 'élan_vital', 'élanVital', 'ÉlanVital', 'élan-vital'];
        yield 'unicode upper' => ['ÉlanVital', 'élan_vital', 'élanVital', 'ÉlanVital', 'élan-vital'];
        yield 'unicode separators' => ['straße größe', 'straße_größe', 'straßeGröße', 'StraßeGröße', 'straße-größe'];
        yield 'lower accented before upper' => ['caféBar', 'café_bar', 'caféBar', 'CaféBar', 'café-bar'];
        yield 'lower before accented upper' => [
            'grandeÉcole', 'grande_école', 'grandeÉcole', 'GrandeÉcole', 'grande-école',
        ];
        yield 'acronym then accented word' => ['HTTPÉcole', 'http_école', 'httpÉcole', 'HttpÉcole', 'http-école'];
        yield 'acronym then accented lower' => ['HTTPSérie', 'http_série', 'httpSérie', 'HttpSérie', 'http-série'];
        yield 'accented acronym then word' => ['ÀÉcole', 'à_école', 'àÉcole', 'ÀÉcole', 'à-école'];
        yield 'sharp s has no single upper case' => ['ßeta', 'ßeta', 'ßeta', 'Sseta', 'ßeta'];
        yield 'digraph is title-cased' => ['ǆemal', 'ǆemal', 'ǆemal', "\u{1C5}emal", 'ǆemal'];
        yield 'ligature is title-cased' => ["\u{FB01}sh", "\u{FB01}sh", "\u{FB01}sh", 'Fish', "\u{FB01}sh"];
        yield 'combining mark' => ["e\u{301}V", "e\u{301}_v", "e\u{301}V", "E\u{301}V", "e\u{301}-v"];
        yield 'caseless script' => ['你好 世界', '你好_世界', '你好世界', '你好世界', '你好-世界'];
    }

    #[DataProvider('conversionProvider')]
    public function testToSnakeCase(string $input, string $snake, string $camel, string $pascal, string $kebab): void
    {
        self::assertSame($snake, StringUtil::toSnakeCase($input));
    }

    #[DataProvider('conversionProvider')]
    public function testToCamelCase(string $input, string $snake, string $camel, string $pascal, string $kebab): void
    {
        self::assertSame($camel, StringUtil::toCamelCase($input));
    }

    #[DataProvider('conversionProvider')]
    public function testToPascalCase(string $input, string $snake, string $camel, string $pascal, string $kebab): void
    {
        self::assertSame($pascal, StringUtil::toPascalCase($input));
    }

    #[DataProvider('conversionProvider')]
    public function testToKebabCase(string $input, string $snake, string $camel, string $pascal, string $kebab): void
    {
        self::assertSame($kebab, StringUtil::toKebabCase($input));
    }

    /**
     * Single-letter words are excluded: PascalCase cannot tell `A_B` from the acronym `AB`.
     *
     * @return iterable<string, array{string}>
     */
    public static function idempotenceProvider(): iterable
    {
        yield 'empty' => [''];
        yield 'snake' => ['first_name'];
        yield 'screaming snake' => ['LAST_NAME'];
        yield 'kebab with spaces' => ['Email-Address and more'];
        yield 'camel' => ['firstName'];
        yield 'pascal' => ['FirstName'];
        yield 'acronym' => ['HTTPServerError'];
        yield 'digits' => ['userID2Name'];
        yield 'word starting with digit' => ['abc_1def_ghi'];
        yield 'unicode' => ['élanVital Straße'];
        yield 'separators only' => ['--__..'];
        yield 'sharp s initial' => ['ßeta'];
        yield 'sharp s initial after word' => ['x_ßy'];
        yield 'digraph initial' => ['ǆemal'];
        yield 'ligature initial after word' => ["ab_\u{FB01}sh"];
    }

    /**
     * @return iterable<string, array{Closure(string): string}>
     */
    public static function converterProvider(): iterable
    {
        yield 'snake' => [StringUtil::toSnakeCase(...)];
        yield 'camel' => [StringUtil::toCamelCase(...)];
        yield 'pascal' => [StringUtil::toPascalCase(...)];
        yield 'kebab' => [StringUtil::toKebabCase(...)];
    }

    /**
     * @return iterable<string, array{Closure(string): string, string}>
     */
    public static function idempotentConversionProvider(): iterable
    {
        foreach (self::converterProvider() as $converterName => [$convert]) {
            foreach (self::idempotenceProvider() as $inputName => [$input]) {
                yield $converterName . ': ' . $inputName => [$convert, $input];
            }
        }
    }

    /**
     * @param Closure(string): string $convert
     */
    #[DataProvider('idempotentConversionProvider')]
    public function testConversionsAreIdempotent(Closure $convert, string $input): void
    {
        $once = $convert($input);

        self::assertSame($once, $convert($once));
    }

    /**
     * @param Closure(string): string $convert
     */
    #[DataProvider('converterProvider')]
    public function testInvalidUtf8IsRejected(Closure $convert): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot split string into words: ');

        $convert("first\xFFname");
    }
}
