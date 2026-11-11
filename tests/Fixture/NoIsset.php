<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class NoIsset
{
    public function __get(string $name): string
    {
        return $name;
    }
}
