<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class IssetOnly
{
    public function __isset(string $name): bool
    {
        return true;
    }
}
