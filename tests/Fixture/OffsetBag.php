<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

use ArrayAccess;

/**
 * An \ArrayAccess object that also has a public property, to show which one answers.
 *
 * @implements ArrayAccess<string, string>
 */
final class OffsetBag implements ArrayAccess
{
    public string $name = 'public-property';

    /** @param array<string, string> $offsets */
    public function __construct(private array $offsets)
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->offsets[$offset]);
    }

    public function offsetGet(mixed $offset): string
    {
        return $this->offsets[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
