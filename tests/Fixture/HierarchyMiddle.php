<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

class HierarchyMiddle extends HierarchyBase
{
    private string $middle = 'middle-private';

    /**
     * @return array<string, mixed>
     */
    public function reveal(): array
    {
        return ['middle' => $this->middle] + parent::reveal();
    }
}
