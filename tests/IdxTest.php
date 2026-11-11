<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use ArrayObject;
use Dranzd\Common\Utils\Idx;
use Dranzd\Common\Utils\Tests\Fixture\Backed;
use Dranzd\Common\Utils\Tests\Fixture\Exploding;
use Dranzd\Common\Utils\Tests\Fixture\ExplodingMagic;
use Dranzd\Common\Utils\Tests\Fixture\HierarchyLeaf;
use Dranzd\Common\Utils\Tests\Fixture\IdxSubject;
use Dranzd\Common\Utils\Tests\Fixture\IssetOnly;
use Dranzd\Common\Utils\Tests\Fixture\MagicBag;
use Dranzd\Common\Utils\Tests\Fixture\NoIsset;
use Dranzd\Common\Utils\Tests\Fixture\OffsetBag;
use Dranzd\Common\Utils\Tests\Fixture\PrecedenceProbe;
use Dranzd\Common\Utils\Tests\Fixture\Unguarded;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SplFixedArray;
use SplObjectStorage;
use stdClass;
use WeakMap;

#[CoversClass(Idx::class)]
final class IdxTest extends TestCase
{
    private const DEFAULT = 'default';

    public function testArrayKeyIsFound(): void
    {
        self::assertSame(1, Idx::get(['a' => 1], 'a', self::DEFAULT));
    }

    public function testArrayIntKeyIsFound(): void
    {
        self::assertSame('x', Idx::get(['x', 'y'], 0, self::DEFAULT));
    }

    public function testStoredNullIsAFoundValue(): void
    {
        self::assertNull(Idx::get(['a' => null], 'a', self::DEFAULT));
    }

    public function testMissingArrayKeyReturnsDefault(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(['a' => 1], 'b', self::DEFAULT));
    }

    public function testArrayAccessObjectIsReadThroughOffsets(): void
    {
        self::assertSame(1, Idx::get(new ArrayObject(['a' => 1]), 'a', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get(new ArrayObject(['a' => 1]), 'b', self::DEFAULT));
    }

    public function testFoundOffsetBeatsPublicProperty(): void
    {
        self::assertSame('offset', Idx::get(new OffsetBag(['name' => 'offset']), 'name', self::DEFAULT));
    }

    public function testMissingOffsetFallsThroughToPublicProperty(): void
    {
        self::assertSame('public-property', Idx::get(new OffsetBag([]), 'name', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get(new OffsetBag([]), 'other', self::DEFAULT));
    }

    /**
     * @return iterable<string, array{object}>
     */
    public static function offsetsRejectingStringKeys(): iterable
    {
        yield 'SplObjectStorage' => [new SplObjectStorage()];
        yield 'WeakMap' => [new WeakMap()];
        yield 'SplFixedArray' => [new SplFixedArray(2)];
    }

    #[DataProvider('offsetsRejectingStringKeys')]
    public function testOffsetTypeErrorMeansNotFound(object $subject): void
    {
        self::assertSame(self::DEFAULT, Idx::get($subject, 'key', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get(['x' => $subject], ['x', 'key'], self::DEFAULT));
    }

    public function testPublicDeclaredPropertyIsFound(): void
    {
        self::assertSame('public-property', Idx::get(new IdxSubject(), 'title', self::DEFAULT));
    }

    public function testPublicNullPropertyIsAFoundValue(): void
    {
        self::assertNull(Idx::get(new IdxSubject(), 'nothing', self::DEFAULT));
    }

    public function testDynamicPropertyIsFoundForStringAndIntKeys(): void
    {
        $object = new stdClass();
        $object->name = 'dynamic';
        $object->{'0'} = 'zero';

        self::assertSame('dynamic', Idx::get($object, 'name', self::DEFAULT));
        self::assertSame('zero', Idx::get($object, 0, self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get($object, 1, self::DEFAULT));
    }

    public function testPublicPropertyBeatsMagicAndMagicBeatsAccessorAndHiddenProperty(): void
    {
        $probe = new PrecedenceProbe();

        self::assertSame('public-property', Idx::get($probe, 'a', self::DEFAULT));
        self::assertSame('magic', Idx::get($probe, 'c', self::DEFAULT));
        self::assertSame('hidden-c', $probe->reveal());
    }

    public function testMagicGetIsUsedWhenIssetIsTrue(): void
    {
        $bag = new MagicBag(['a' => 'magic', 'n' => null]);

        self::assertSame('magic', Idx::get($bag, 'a', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get($bag, 'n', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get($bag, 'missing', self::DEFAULT));
    }

    public function testMagicGetWithoutIssetIsNotUsed(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(new NoIsset(), 'anything', self::DEFAULT));
    }

    public function testMagicIssetWithoutGetIsNotUsed(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(new IssetOnly(), 'anything', self::DEFAULT));
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function accessorCases(): iterable
    {
        yield 'getKey() beats a private property' => ['label', 'getter-label'];
        yield 'isKey() beats a private property' => ['active', false];
        yield 'getKey() beats isKey() and key()' => ['foo', 'get-foo'];
        yield 'isKey() beats key()' => ['bar', 'is-bar'];
        yield 'key() beats a private property' => ['summary', 'plain-method'];
        yield 'method with only optional parameters' => ['maybe', 'optional-none'];
    }

    #[DataProvider('accessorCases')]
    public function testAccessorMethods(string $key, mixed $expected): void
    {
        self::assertSame($expected, Idx::get(new IdxSubject(), $key, self::DEFAULT));
    }

    public function testHasKeyAccessorIsUsed(): void
    {
        self::assertTrue(Idx::get(new Backed(), 'items', self::DEFAULT));
    }

    #[TestWith(['needsArg', 'hidden-needs-arg'])]
    #[TestWith(['statically', 'hidden-statically'])]
    #[TestWith(['guarded', 'hidden-guarded'])]
    #[TestWith(['__toString', 'hidden-to-string'])]
    public function testUnusableMethodsFallThroughToTheHiddenProperty(string $key, string $expected): void
    {
        self::assertSame($expected, Idx::get(new IdxSubject(), $key, self::DEFAULT));
    }

    #[TestWith(['delete'])]
    #[TestWith(['ghost'])]
    #[TestWith(['ready'])]
    #[TestWith(['items'])]
    public function testMethodsWithoutABackingPropertyAreNeverCalled(string $key): void
    {
        $subject = new Unguarded();

        self::assertSame(self::DEFAULT, Idx::get($subject, $key, self::DEFAULT));
        self::assertSame(0, $subject->calls);
    }

    public function testGeneratorIsNotAdvanced(): void
    {
        $generator = (static function (): \Generator {
            yield 1;
            yield 2;
        })();
        $before = $generator->current();

        self::assertSame(self::DEFAULT, Idx::get($generator, 'next', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get($generator, 'send', self::DEFAULT));
        self::assertSame($before, $generator->current());
    }

    public function testStaticPropertiesAreNeverRead(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(new Unguarded(), 'total', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get(new Unguarded(), 'hiddenTotal', self::DEFAULT));
        self::assertSame(8, Unguarded::reveal());
    }

    public function testEmptyKeyOnAnObjectReturnsDefault(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(new IdxSubject(), '', self::DEFAULT));
    }

    public function testExceptionsFromUserCodePropagate(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('__get exploded');

        Idx::get(new ExplodingMagic(), 'anything');
    }

    public function testExceptionFromAnAccessorPropagates(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('accessor exploded');

        Idx::get(new Exploding(), 'boom');
    }

    public function testExceptionFromOffsetGetPropagates(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('offsetGet exploded');

        Idx::get(new Exploding(), 'offset');
    }

    public function testExceptionFromCountPropagates(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('count exploded');

        Idx::get(['k' => new Exploding()], 'k', self::DEFAULT, true);
    }

    public function testNonPublicPropertiesAtAnyLevelAreReadByReflection(): void
    {
        $leaf = new HierarchyLeaf();

        self::assertSame(1, Idx::get($leaf, 'count', self::DEFAULT));
        self::assertSame('middle-private', Idx::get($leaf, 'middle', self::DEFAULT));
        self::assertSame('base-secret', Idx::get($leaf, 'secret', self::DEFAULT));
        self::assertSame('base-shared', Idx::get($leaf, 'shared', self::DEFAULT));
        self::assertSame('frozen', Idx::get($leaf, 'frozen', self::DEFAULT));
    }

    public function testUninitializedTypedPropertyCountsAsMissing(): void
    {
        $leaf = new HierarchyLeaf();
        self::assertSame(self::DEFAULT, Idx::get($leaf, 'uninitialized', self::DEFAULT));

        $leaf->initialize(7);
        self::assertSame(7, Idx::get($leaf, 'uninitialized', self::DEFAULT));

        $subject = new IdxSubject();
        self::assertSame(self::DEFAULT, Idx::get($subject, 'uninitialized', self::DEFAULT));
    }

    public function testMissingPropertyOnObjectReturnsDefault(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(new IdxSubject(), 'nope', self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get(new IdxSubject(), 0, self::DEFAULT));
    }

    #[TestWith([null])]
    #[TestWith(['string'])]
    #[TestWith([42])]
    #[TestWith([true])]
    public function testNonContainerSubjectsReturnDefault(mixed $subject): void
    {
        self::assertSame(self::DEFAULT, Idx::get($subject, 'a', self::DEFAULT));
    }

    public function testPathMixingArraysAndObjects(): void
    {
        $object = new stdClass();
        $object->items = new ArrayObject([['sku' => new MagicBag(['price' => 9])]]);
        $data = ['order' => $object];

        self::assertSame(9, Idx::get($data, ['order', 'items', 0, 'sku', 'price'], self::DEFAULT));
        self::assertSame(self::DEFAULT, Idx::get($data, ['order', 'items', 1, 'sku'], self::DEFAULT));
    }

    public function testPathThroughAccessorAndHiddenProperty(): void
    {
        $data = ['leaf' => new HierarchyLeaf(), 'subject' => new IdxSubject()];

        self::assertSame('base-secret', Idx::get($data, ['leaf', 'secret']));
        self::assertSame('getter-label', Idx::get($data, ['subject', 'label']));
    }

    /**
     * @return iterable<string, array{mixed, list<mixed>}>
     */
    public static function brokenPaths(): iterable
    {
        yield 'missing first segment' => [['a' => ['b' => 1]], ['x', 'b']];
        yield 'missing middle segment' => [['a' => ['b' => 1]], ['a', 'x', 'b']];
        yield 'missing last segment' => [['a' => ['b' => 1]], ['a', 'x']];
        yield 'null intermediate value' => [['a' => null], ['a', 'b']];
        yield 'scalar intermediate value' => [['a' => 5], ['a', 'b']];
        yield 'path deeper than data' => [['a' => 1], ['a', 'b', 'c']];
    }

    /**
     * @param list<mixed> $path
     */
    #[DataProvider('brokenPaths')]
    public function testBrokenPathReturnsDefault(mixed $subject, array $path): void
    {
        self::assertSame(self::DEFAULT, Idx::get($subject, $path, self::DEFAULT));
    }

    public function testNullAtTheEndOfAPathIsAFoundValue(): void
    {
        self::assertNull(Idx::get(['a' => ['b' => null]], ['a', 'b'], self::DEFAULT));
    }

    public function testEmptyPathReturnsDefault(): void
    {
        self::assertSame(self::DEFAULT, Idx::get(['a' => 1], [], self::DEFAULT));
    }

    /**
     * @param array<mixed> $path
     */
    #[TestWith([['a', 1.5], 'float'])]
    #[TestWith([['a', null], 'null'])]
    #[TestWith([['a', ['b']], 'array'])]
    #[TestWith([[new stdClass()], 'stdClass'])]
    public function testNonScalarPathSegmentsAreRejected(array $path, string $type): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($type . ' given');

        Idx::get(['a' => ['b' => 1]], $path);
    }

    public function testWholePathIsValidatedBeforeWalking(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('float given');

        Idx::get([], ['missing', 1.5], self::DEFAULT);
    }

    /**
     * @return iterable<string, array{mixed, bool}>
     */
    public static function emptyMatrix(): iterable
    {
        yield 'null' => [null, true];
        yield 'empty string' => ['', true];
        yield 'empty array' => [[], true];
        yield 'empty ArrayObject' => [new ArrayObject(), true];
        yield 'empty Countable' => [new \SplObjectStorage(), true];
        yield 'zero' => [0, false];
        yield 'zero float' => [0.0, false];
        yield 'string zero' => ['0', false];
        yield 'false' => [false, false];
        yield 'blank string' => [' ', false];
        yield 'non-empty array' => [[0], false];
        yield 'non-empty ArrayObject' => [new ArrayObject([1]), false];
        yield 'plain object' => [new stdClass(), false];
    }

    #[DataProvider('emptyMatrix')]
    public function testUseDefaultOnEmpty(mixed $stored, bool $isEmpty): void
    {
        $expected = $isEmpty ? self::DEFAULT : $stored;

        self::assertSame($expected, Idx::get(['k' => $stored], 'k', self::DEFAULT, true));
        self::assertSame($expected, Idx::get(['x' => ['k' => $stored]], ['x', 'k'], self::DEFAULT, true));
        self::assertSame($stored, Idx::get(['k' => $stored], 'k', self::DEFAULT, false));
    }

    public function testUseDefaultOnEmptyAppliesToEveryResolutionRule(): void
    {
        $object = new stdClass();
        $object->empty = '';

        self::assertSame(self::DEFAULT, Idx::get($object, 'empty', self::DEFAULT, true));
        self::assertSame(self::DEFAULT, Idx::get(new ArrayObject(['e' => []]), 'e', self::DEFAULT, true));
        self::assertSame(self::DEFAULT, Idx::get(new IdxSubject(), 'nothing', self::DEFAULT, true));
        self::assertSame(0, Idx::get(new MagicBag(['z' => 0]), 'z', self::DEFAULT, true));
    }
}
