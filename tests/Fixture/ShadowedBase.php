<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

class ShadowedBase
{
    private string $hidden = 'base';

    public function baseValue(): string
    {
        return $this->hidden;
    }
}
