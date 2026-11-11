<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

final class ConstructorGuarded
{
    use SomeTrait;

    public bool $constructed = false;

    public function __construct()
    {
        $this->constructed = true;
    }
}
