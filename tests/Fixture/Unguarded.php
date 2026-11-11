<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

/**
 * Methods with no property behind them: Idx must not call any of these.
 */
final class Unguarded
{
    public static int $total = 7;
    private static int $hiddenTotal = 8;
    public int $calls = 0;

    public function delete(): void
    {
        ++$this->calls;
    }

    public function getGhost(): string
    {
        ++$this->calls;

        return 'ghost';
    }

    public function isReady(): bool
    {
        ++$this->calls;

        return true;
    }

    public function hasItems(): bool
    {
        ++$this->calls;

        return true;
    }

    public static function reveal(): int
    {
        return self::$hiddenTotal;
    }
}
