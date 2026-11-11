<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

use ArrayAccess;
use Countable;
use RuntimeException;

/**
 * Throws from every piece of user code that Idx may call.
 *
 * @implements ArrayAccess<string, string>
 */
final class Exploding implements ArrayAccess, Countable
{
    private string $boom = 'hidden-boom';

    public function offsetExists(mixed $offset): bool
    {
        return $offset === 'offset';
    }

    public function offsetGet(mixed $offset): string
    {
        throw new RuntimeException('offsetGet exploded');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public function count(): int
    {
        throw new RuntimeException('count exploded');
    }

    public function getBoom(): string
    {
        throw new RuntimeException('accessor exploded');
    }

    public function reveal(): string
    {
        return $this->boom;
    }
}
