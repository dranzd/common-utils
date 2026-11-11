<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

/**
 * Answers every key through the magic methods, to show what beats them and what they beat.
 */
final class PrecedenceProbe
{
    public string $a = 'public-property';
    private string $c = 'hidden-c';

    public function getC(): string
    {
        return 'accessor-c';
    }

    public function __isset(string $name): bool
    {
        return true;
    }

    public function __get(string $name): string
    {
        return 'magic';
    }

    public function reveal(): string
    {
        return $this->c;
    }
}
