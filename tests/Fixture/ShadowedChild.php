<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class ShadowedChild extends ShadowedBase
{
    private string $hidden = 'child';

    public function childValue(): string
    {
        return $this->hidden;
    }
}
