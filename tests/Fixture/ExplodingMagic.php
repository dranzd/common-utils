<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

use RuntimeException;

final class ExplodingMagic
{
    public function __isset(string $name): bool
    {
        return true;
    }

    public function __get(string $name): never
    {
        throw new RuntimeException('__get exploded');
    }
}
