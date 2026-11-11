<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

class HierarchyBase
{
    private string $secret = 'base-secret';
    protected string $shared = 'base-shared';
    private int $uninitialized;

    public function initialize(int $value): void
    {
        $this->uninitialized = $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function reveal(): array
    {
        return [
            'secret' => $this->secret,
            'shared' => $this->shared,
            'uninitialized' => $this->uninitialized ?? null,
        ];
    }
}
