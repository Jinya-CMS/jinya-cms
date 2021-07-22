<?php

namespace Jinya\Tests\Utils;

use App\Utils\ClassResolver;
use Exception;
use PHPUnit\Framework\TestCase;

class ClassResolverTest extends TestCase
{

    public function testLoadClassesNoEntriesDueDefaultPath(): void
    {
        $classes = ClassResolver::loadClasses(__DIR__ . DIRECTORY_SEPARATOR . 'ClassResolverData');
        $this->assertIsArray($classes, 'The result must be an array');

        foreach ($classes as $class) {
            $this->assertStringStartsNotWith('App\\Utils\\ClassResolverData', $class);
        }
    }

    public function testLoadClassesOneResultDueToMatchingPath(): void
    {
        $classes = ClassResolver::loadClasses(__DIR__ . '../../src', __DIR__);
        $this->assertIsArray($classes, 'The result must be an array');

        $count = 0;
        foreach ($classes as $class) {
            if (str_starts_with($class, 'App\\Tests')) {
                $count++;
            }
        }
        $this->assertGreaterThanOrEqual(3, $count, 'There should be at least one class loaded');
    }

    public function testLoadClassesInvalidPath(): void
    {
        try {
            $classes = ClassResolver::loadClasses('/this/path/is/invalid', 'tests');
            $this->assertIsArray($classes, 'The result must be an array');
        } catch (Exception) {
            $this->fail('Should not throw');
        }
    }
}
