<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests\Fixture;

/**
 * Exercises every accessor rule of Idx::get() and their precedence.
 *
 * Every accessor has a non-public property behind it, which is what lets Idx call it. The properties
 * hold a different value from the accessor so a test can tell which one answered; reveal() keeps them
 * "used" for static analysis.
 */
final class IdxSubject
{
    public string $title = 'public-property';
    public ?string $nothing = null;
    private string $label = 'hidden-label';
    private bool $active = true;
    private string $foo = 'hidden-foo';
    private string $bar = 'hidden-bar';
    private string $summary = 'hidden-summary';
    private string $maybe = 'hidden-maybe';
    private string $needsArg = 'hidden-needs-arg';
    private string $statically = 'hidden-statically';
    private string $guarded = 'hidden-guarded';
    private int $uninitialized;

    public function __construct(private string $__toString = 'hidden-to-string')
    {
    }

    public function initialize(int $value): void
    {
        $this->uninitialized = $value;
    }

    public function getTitle(): string
    {
        return 'getter-title';
    }

    public function getLabel(): string
    {
        return 'getter-label';
    }

    public function isActive(): bool
    {
        return false;
    }

    public function getFoo(): string
    {
        return 'get-foo';
    }

    public function isFoo(): string
    {
        return 'is-foo';
    }

    public function foo(): string
    {
        return 'plain-foo';
    }

    public function isBar(): string
    {
        return 'is-bar';
    }

    public function bar(): string
    {
        return 'plain-bar';
    }

    public function summary(): string
    {
        return 'plain-method';
    }

    public function maybe(?string $argument = null): string
    {
        return 'optional-' . ($argument ?? 'none');
    }

    public function needsArg(string $argument): string
    {
        return $argument;
    }

    public static function statically(): string
    {
        return 'static-method';
    }

    public function __toString(): string
    {
        return 'string-method';
    }

    protected function guarded(): string
    {
        return 'protected-method';
    }

    /**
     * @return list<mixed>
     */
    public function reveal(): array
    {
        return [
            $this->label,
            $this->active,
            $this->foo,
            $this->bar,
            $this->summary,
            $this->maybe,
            $this->needsArg,
            $this->statically,
            $this->guarded(),
            $this->guarded,
            $this->__toString,
            $this->uninitialized ?? null,
        ];
    }
}
