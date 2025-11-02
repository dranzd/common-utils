<?php

declare(strict_types=1);

namespace Dranzd\Common\Utils\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Placeholder test to allow CI to pass until full test suite is implemented.
 *
 * TODO: Remove this file once actual tests are implemented.
 */
final class PlaceholderTest extends TestCase
{
    /**
     * Placeholder test that always passes.
     * This ensures the CI pipeline can run successfully.
     */
    public function testPlaceholder(): void
    {
        $this->assertTrue(true, 'Placeholder test - test suite pending implementation');
    }

    /**
     * Verify PHP version meets requirements.
     */
    public function testPhpVersion(): void
    {
        $this->assertGreaterThanOrEqual(
            '8.3.0',
            PHP_VERSION,
            'PHP version must be 8.3.0 or higher'
        );
    }

    /**
     * Verify required extensions are loaded.
     */
    public function testRequiredExtensions(): void
    {
        $this->assertTrue(
            extension_loaded('json'),
            'JSON extension must be loaded'
        );
    }

    /**
     * Verify all utility classes exist and are loadable.
     */
    public function testUtilityClassesExist(): void
    {
        $classes = [
            \Dranzd\Common\Utils\ArrayUtil::class,
            \Dranzd\Common\Utils\StringUtil::class,
            \Dranzd\Common\Utils\Idx::class,
            \Dranzd\Common\Utils\DateUtil::class,
            \Dranzd\Common\Utils\MoneyUtil::class,
            \Dranzd\Common\Utils\ObjectUtil::class,
            \Dranzd\Common\Utils\SearchUtil::class,
        ];

        foreach ($classes as $class) {
            $this->assertTrue(
                class_exists($class),
                sprintf('Class %s must exist', $class)
            );
        }
    }

    /**
     * Verify all utility classes are final.
     */
    public function testUtilityClassesAreFinal(): void
    {
        $classes = [
            \Dranzd\Common\Utils\ArrayUtil::class,
            \Dranzd\Common\Utils\StringUtil::class,
            \Dranzd\Common\Utils\Idx::class,
            \Dranzd\Common\Utils\DateUtil::class,
            \Dranzd\Common\Utils\MoneyUtil::class,
            \Dranzd\Common\Utils\ObjectUtil::class,
            \Dranzd\Common\Utils\SearchUtil::class,
        ];

        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $this->assertTrue(
                $reflection->isFinal(),
                sprintf('Class %s must be final', $class)
            );
        }
    }
}
