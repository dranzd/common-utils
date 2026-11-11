<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class HierarchyLeaf extends HierarchyMiddle
{
    public string $open = 'leaf-public';
    private int $count = 1;
    private readonly string $frozen;

    public function __construct()
    {
        $this->frozen = 'frozen';
    }

    /**
     * @return array<string, mixed>
     */
    public function reveal(): array
    {
        return ['count' => $this->count, 'frozen' => $this->frozen] + parent::reveal();
    }
}
