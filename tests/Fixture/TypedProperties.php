<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class TypedProperties
{
    public int $int = 0;
    public ?int $nullableInt = null;
    public int|string $intOrString = 0;
    public int|float $number = 0;
    public float $float = 0.0;
}
