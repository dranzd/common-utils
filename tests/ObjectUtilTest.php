<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use Dranzd\Common\Utils\ObjectUtil;
use Dranzd\Common\Utils\Tests\Fixture\AbstractShape;
use Dranzd\Common\Utils\Tests\Fixture\ConstructorGuarded;
use Dranzd\Common\Utils\Tests\Fixture\HierarchyLeaf;
use Dranzd\Common\Utils\Tests\Fixture\ShadowedChild;
use Dranzd\Common\Utils\Tests\Fixture\SomeTrait;
use Dranzd\Common\Utils\Tests\Fixture\Suit;
use Dranzd\Common\Utils\Tests\Fixture\TypedProperties;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use TypeError;
use stdClass;

#[CoversClass(ObjectUtil::class)]
final class ObjectUtilTest extends TestCase
{
    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function readableProperties(): iterable
    {
        yield 'public on the object class' => ['open', 'leaf-public'];
        yield 'private on the object class' => ['count', 1];
        yield 'private on the parent' => ['middle', 'middle-private'];
        yield 'private on the grandparent' => ['secret', 'base-secret'];
        yield 'protected on the grandparent' => ['shared', 'base-shared'];
    }

    #[DataProvider('readableProperties')]
    public function testGetPropertyValueReadsAnyVisibilityAtAnyLevel(string $property, mixed $expected): void
    {
        self::assertSame($expected, ObjectUtil::getPropertyValue(new HierarchyLeaf(), $property));
    }

    public function testGetPropertyValueReadsDynamicProperties(): void
    {
        $object = new stdClass();
        $object->dynamic = 'value';

        self::assertSame('value', ObjectUtil::getPropertyValue($object, 'dynamic'));
    }

    public function testGetPropertyValueThrowsForMissingProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(HierarchyLeaf::class . '::$nope does not exist');

        ObjectUtil::getPropertyValue(new HierarchyLeaf(), 'nope');
    }

    public function testGetPropertyValueThrowsForUninitializedTypedProperty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Property ' . HierarchyLeaf::class . '::$uninitialized must not be accessed before initialization.'
        );

        ObjectUtil::getPropertyValue(new HierarchyLeaf(), 'uninitialized');
    }

    public function testSetPropertyValueWritesAnyVisibilityAtAnyLevel(): void
    {
        $leaf = new HierarchyLeaf();

        ObjectUtil::setPropertyValue($leaf, 'open', 'a');
        ObjectUtil::setPropertyValue($leaf, 'count', 5);
        ObjectUtil::setPropertyValue($leaf, 'middle', 'b');
        ObjectUtil::setPropertyValue($leaf, 'secret', 'c');
        ObjectUtil::setPropertyValue($leaf, 'uninitialized', 9);

        self::assertSame('a', $leaf->open);
        self::assertSame(5, $leaf->reveal()['count']);
        self::assertSame('b', ObjectUtil::getPropertyValue($leaf, 'middle'));
        self::assertSame('c', ObjectUtil::getPropertyValue($leaf, 'secret'));
        self::assertSame(9, ObjectUtil::getPropertyValue($leaf, 'uninitialized'));
    }

    public function testSetPropertyValueWritesExistingDynamicProperties(): void
    {
        $object = new stdClass();
        $object->dynamic = 'old';

        ObjectUtil::setPropertyValue($object, 'dynamic', 'new');

        self::assertSame('new', $object->dynamic);
    }

    public function testSetPropertyValueThrowsForMissingProperty(): void
    {
        $leaf = new HierarchyLeaf();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('::$nope does not exist');

        ObjectUtil::setPropertyValue($leaf, 'nope', 1);
    }

    public function testSetPropertyValueWrapsReadonlyViolation(): void
    {
        $leaf = new HierarchyLeaf();

        try {
            ObjectUtil::setPropertyValue($leaf, 'frozen', 'changed');
            self::fail('Expected an exception.');
        } catch (InvalidArgumentException $exception) {
            self::assertStringContainsString('Cannot set property', $exception->getMessage());
            self::assertStringContainsString('readonly', $exception->getMessage());
            self::assertInstanceOf(\Error::class, $exception->getPrevious());
            self::assertNotInstanceOf(TypeError::class, $exception->getPrevious());
        }

        self::assertSame('frozen', $leaf->reveal()['frozen']);
    }

    public function testSetPropertyValueWrapsTypeMismatch(): void
    {
        $leaf = new HierarchyLeaf();

        try {
            ObjectUtil::setPropertyValue($leaf, 'count', 'not an int');
            self::fail('Expected an exception.');
        } catch (InvalidArgumentException $exception) {
            self::assertInstanceOf(TypeError::class, $exception->getPrevious());
        }

        self::assertSame(1, $leaf->reveal()['count']);
    }

    public function testSetPropertyValueCoercesScalarsWithNonStrictRules(): void
    {
        $typed = new TypedProperties();

        ObjectUtil::setPropertyValue($typed, 'int', '5');
        ObjectUtil::setPropertyValue($typed, 'float', 2);
        ObjectUtil::setPropertyValue($typed, 'nullableInt', 3.0);

        self::assertSame(5, $typed->int);
        self::assertSame(2.0, $typed->float);
        self::assertSame(3, $typed->nullableInt);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function intOnlyProperties(): iterable
    {
        yield 'int' => ['int'];
        yield 'nullable int' => ['nullableInt'];
        yield 'int or string' => ['intOrString'];
    }

    #[DataProvider('intOnlyProperties')]
    public function testSetPropertyValueRejectsAFractionalFloatForIntProperties(string $property): void
    {
        $typed = new TypedProperties();

        try {
            ObjectUtil::setPropertyValue($typed, $property, 1.5);
            self::fail('Expected an exception.');
        } catch (InvalidArgumentException $exception) {
            self::assertStringContainsString(TypedProperties::class . '::$' . $property, $exception->getMessage());
            self::assertStringContainsString('1.5', $exception->getMessage());
            self::assertNull($exception->getPrevious());
        }

        self::assertNotSame(1, ObjectUtil::getPropertyValue($typed, $property));
    }

    #[TestWith(['float'])]
    #[TestWith(['number'])]
    public function testSetPropertyValueKeepsAFractionalFloatWhereFloatIsAccepted(string $property): void
    {
        $typed = new TypedProperties();

        ObjectUtil::setPropertyValue($typed, $property, 1.5);

        self::assertSame(1.5, ObjectUtil::getPropertyValue($typed, $property));
    }

    public function testSetPropertyValueWritesAnUninitializedReadonlyPropertyOfAHydratedInstance(): void
    {
        $leaf = ObjectUtil::newInstanceWithoutConstructor(HierarchyLeaf::class);

        ObjectUtil::setPropertyValue($leaf, 'frozen', 'hydrated');

        self::assertSame('hydrated', ObjectUtil::getPropertyValue($leaf, 'frozen'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('readonly');

        ObjectUtil::setPropertyValue($leaf, 'frozen', 'again');
    }

    public function testNearestPrivateDeclarationHidesTheParentsProperty(): void
    {
        $child = new ShadowedChild();

        self::assertSame('child', ObjectUtil::getPropertyValue($child, 'hidden'));

        ObjectUtil::setPropertyValue($child, 'hidden', 'changed');

        self::assertSame('changed', $child->childValue());
        self::assertSame('base', $child->baseValue());
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function declaredPropertyPresence(): iterable
    {
        yield 'declared on the object class' => ['open', true];
        yield 'private on the grandparent' => ['secret', true];
        yield 'uninitialized but declared' => ['uninitialized', true];
        yield 'missing' => ['nope', false];
    }

    #[DataProvider('declaredPropertyPresence')]
    public function testHasPropertyForDeclaredProperties(string $property, bool $expected): void
    {
        self::assertSame($expected, ObjectUtil::hasProperty(new HierarchyLeaf(), $property));
    }

    public function testHasPropertyForDynamicProperties(): void
    {
        $object = new stdClass();
        $object->dynamic = null;

        self::assertTrue(ObjectUtil::hasProperty($object, 'dynamic'));
        self::assertFalse(ObjectUtil::hasProperty($object, 'other'));
    }

    public function testNewInstanceWithoutConstructorSkipsTheConstructor(): void
    {
        $instance = ObjectUtil::newInstanceWithoutConstructor(ConstructorGuarded::class);

        self::assertFalse($instance->constructed);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function uninstantiableClasses(): iterable
    {
        yield 'unknown class' => ['Dranzd\\Nope\\Missing', 'Class "Dranzd\\Nope\\Missing" does not exist.'];
        yield 'interface' => [\Countable::class, '"Countable" is an interface, not a class'];
        yield 'trait' => [SomeTrait::class, 'SomeTrait" is a trait, not a class'];
        yield 'abstract class' => [AbstractShape::class, 'AbstractShape" is abstract'];
        yield 'enum' => [Suit::class, 'Suit" is an enum'];
        yield 'internal final class' => [\Closure::class, 'Internal class "Closure" cannot be instantiated'];
    }

    #[DataProvider('uninstantiableClasses')]
    public function testNewInstanceWithoutConstructorRejectsUninstantiableClasses(
        string $className,
        string $message
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        /** @phpstan-var class-string $className */
        ObjectUtil::newInstanceWithoutConstructor($className);
    }

    public function testNewInstanceWithoutConstructorKeepsTheReflectionExceptionOfInternalClasses(): void
    {
        try {
            ObjectUtil::newInstanceWithoutConstructor(\Closure::class);
            self::fail('Expected an exception.');
        } catch (InvalidArgumentException $exception) {
            self::assertInstanceOf(ReflectionException::class, $exception->getPrevious());
        }
    }

    public function testNewInstanceWithoutConstructorBuildsAnonymousClasses(): void
    {
        $anonymous = new class {
        };

        self::assertNotSame($anonymous, ObjectUtil::newInstanceWithoutConstructor($anonymous::class));
    }

    public function testErrorMessagesNeverEchoTheNulByteOfAnonymousClassNames(): void
    {
        $anonymous = new class {
        };
        $name = (new \ReflectionObject($anonymous))->getName();

        self::assertStringContainsString("\0", $name);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "class@anonymous" does not exist.');

        /** @phpstan-var class-string $missing */
        $missing = $name . '-gone';
        ObjectUtil::newInstanceWithoutConstructor($missing);
    }

    public function testStaticPropertiesAreNotInstanceState(): void
    {
        $object = new class {
            public static int $counter = 1;
        };

        self::assertFalse(ObjectUtil::hasProperty($object, 'counter'));
    }

    public function testStaticPropertiesCannotBeRead(): void
    {
        $object = new class {
            public static int $counter = 1;
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('does not exist');

        ObjectUtil::getPropertyValue($object, 'counter');
    }

    public function testNewDynamicPropertiesCannotBeCreated(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('::$fresh does not exist');

        ObjectUtil::setPropertyValue(new stdClass(), 'fresh', 1);
    }

    public function testDeprecatedInstanceReturnsTheSameObject(): void
    {
        self::assertSame(ObjectUtil::instance(), ObjectUtil::instance());
    }

    public function testDeprecatedSingletonStyleCallsStillWork(): void
    {
        $object = new HierarchyLeaf();

        // Former signatures took a trailing ReflectionClass; extra arguments are ignored.
        self::legacy('setPropertyValue')($object, 'secret', 'changed', new \ReflectionClass($object));

        self::assertSame('changed', self::legacy('getPropertyValue')($object, 'secret'));
        self::assertSame('changed', self::legacy('valueOfProperty')($object, 'secret'));
        self::assertInstanceOf(
            HierarchyLeaf::class,
            self::legacy('newInstanceWithoutConstructor')(HierarchyLeaf::class),
        );
    }

    /**
     * A method called on the singleton instance, the way pre-1.0 code calls it.
     */
    private static function legacy(string $method): callable
    {
        $callable = [ObjectUtil::instance(), $method];
        self::assertIsCallable($callable);

        return $callable;
    }

    public function testDeprecatedValueOfPropertyIsAnAliasOfGetPropertyValue(): void
    {
        $object = new HierarchyLeaf();

        self::assertSame(
            ObjectUtil::getPropertyValue($object, 'secret'),
            ObjectUtil::valueOfProperty($object, 'secret'),
        );
    }
}
