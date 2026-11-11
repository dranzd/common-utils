<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class MagicBag
{
    /** @param array<string, mixed> $data */
    public function __construct(private array $data)
    {
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name];
    }
}
