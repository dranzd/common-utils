<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

/**
 * Accessors that do have a backing property; hasItems() is the third candidate, after get and is.
 */
final class Backed
{
    private bool $items = false;

    public function hasItems(): bool
    {
        return true;
    }

    public function reveal(): bool
    {
        return $this->items;
    }
}
