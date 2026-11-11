<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use Dranzd\Common\Utils\SearchUtil;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchUtil::class)]
final class SearchUtilTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function escapes(): iterable
    {
        yield 'plain text' => ['john', '\\', 'john'];
        yield 'percent' => ['50%', '\\', '50\\%'];
        yield 'underscore' => ['a_b', '\\', 'a\\_b'];
        yield 'escape char first' => ['a\\%', '\\', 'a\\\\\\%'];
        yield 'custom escape char' => ['50%!_', '!', '50!%!!!_'];
        yield 'multibyte escape char' => ['50%', '§', '50§%'];
        yield 'multibyte text' => ['café_', '\\', 'café\\_'];
        yield 'empty' => ['', '\\', ''];
    }

    #[DataProvider('escapes')]
    public function testEscape(string $value, string $escapeChar, string $expected): void
    {
        self::assertSame($expected, SearchUtil::escape($value, $escapeChar));
    }

    public function testEscapeUsesBackslashByDefault(): void
    {
        self::assertSame('a\\%', SearchUtil::escape('a%'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidEscapeChars(): iterable
    {
        yield 'empty' => [''];
        yield 'two characters' => ['ab'];
        yield 'two multibyte characters' => ['§§'];
        yield 'percent' => ['%'];
        yield 'underscore' => ['_'];
        yield 'space' => [' '];
        yield 'tab' => ["\t"];
        yield 'newline' => ["\n"];
        yield 'no-break space' => ["\u{a0}"];
        yield 'ideographic space' => ["\u{3000}"];
        yield 'nul' => ["\0"];
        yield 'escape control character' => ["\x1b"];
        yield 'delete' => ["\x7f"];
        yield 'lone continuation byte' => ["\xA9"];
        yield 'invalid lead byte' => ["\xff"];
        yield 'truncated multibyte sequence' => ["\xC3"];
    }

    #[DataProvider('invalidEscapeChars')]
    public function testInvalidEscapeCharThrows(string $escapeChar): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Escape character must be one valid UTF-8 character');

        SearchUtil::escape('a', $escapeChar);
    }

    #[DataProvider('invalidEscapeChars')]
    public function testInvalidEscapeCharThrowsEvenWithInvalidUtf8Value(string $escapeChar): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Escape character must be');

        SearchUtil::escape("caf\xff", $escapeChar);
    }

    #[DataProvider('invalidEscapeChars')]
    public function testEveryEntryPointRejectsInvalidEscapeChar(string $escapeChar): void
    {
        $calls = [
            static fn (): string => SearchUtil::toLoose('a', $escapeChar),
            static fn (): string => SearchUtil::toLoose('', $escapeChar),
            static fn (): string => SearchUtil::toLooseIncludeEnds('a', $escapeChar),
            static fn (): string => SearchUtil::toLooseIncludeLeft('a', $escapeChar),
            static fn (): string => SearchUtil::toLooseIncludeRight('a', $escapeChar),
        ];

        foreach ($calls as $call) {
            try {
                $call();
                self::fail('Expected InvalidArgumentException.');
            } catch (\InvalidArgumentException $e) {
                self::assertStringContainsString('Escape character must be', $e->getMessage());
            }
        }
    }

    public function testEscapeRejectsInvalidUtf8Value(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be valid UTF-8.');

        SearchUtil::escape("caf\xff");
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function loosePatterns(): iterable
    {
        yield 'collapses whitespace and trims' => ['  john   smith ', 'john%smith'];
        yield 'multibyte preserved' => ['café olé', 'café%olé'];
        yield 'wildcards escaped' => ['50% off_sale', '50\\%%off\\_sale'];
        yield 'backslash escaped' => ['a\\b', 'a\\\\b'];
        yield 'tabs and newlines' => ["a\tb\nc", 'a%b%c'];
        yield 'unicode spaces' => ["a\u{3000}b\u{a0}c", 'a%b%c'];
        yield 'single term' => ['john', 'john'];
        yield 'empty' => ['', ''];
        yield 'whitespace only' => [" \t\n ", ''];
        yield 'control characters are kept' => ["a\x01b \x7fc", "a\x01b%\x7fc"];
    }

    #[DataProvider('loosePatterns')]
    public function testToLoose(string $filter, string $expected): void
    {
        self::assertSame($expected, SearchUtil::toLoose($filter));
    }

    public function testToLooseWithCustomEscapeChar(): void
    {
        self::assertSame('50!%%x!!', SearchUtil::toLoose('50% x!', '!'));
    }

    public function testToLooseRejectsInvalidUtf8(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Filter must be valid UTF-8.');

        SearchUtil::toLoose("caf\xff");
    }

    public function testToLooseRejectsInvalidEscapeCharEvenForEmptyFilter(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        SearchUtil::toLoose('', '%');
    }

    /**
     * @return iterable<string, array{string, string, string, string}>
     */
    public static function wrapped(): iterable
    {
        yield 'term' => ['john smith', '%john%smith%', '%john%smith', 'john%smith%'];
        yield 'escaped' => ['50%', '%50\\%%', '%50\\%', '50\\%%'];
        yield 'empty matches all' => ['', '%', '%', '%'];
        yield 'whitespace matches all' => ['  ', '%', '%', '%'];
    }

    #[DataProvider('wrapped')]
    public function testIncludeVariants(string $filter, string $ends, string $left, string $right): void
    {
        self::assertSame($ends, SearchUtil::toLooseIncludeEnds($filter));
        self::assertSame($left, SearchUtil::toLooseIncludeLeft($filter));
        self::assertSame($right, SearchUtil::toLooseIncludeRight($filter));
    }

    public function testIncludeVariantsAcceptCustomEscapeChar(): void
    {
        self::assertSame('%a!%%', SearchUtil::toLooseIncludeEnds('a%', '!'));
        self::assertSame('%a!%', SearchUtil::toLooseIncludeLeft('a%', '!'));
        self::assertSame('a!%%', SearchUtil::toLooseIncludeRight('a%', '!'));
    }
}
