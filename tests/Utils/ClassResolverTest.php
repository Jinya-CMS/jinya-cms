<?php

namespace Utils;

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
        $classes = ClassResolver::loadClasses(__DIR__ . DIRECTORY_SEPARATOR . 'ClassResolverData', 'tests');
        $this->assertIsArray($classes, 'The result must be an array');

        $count = 0;
        foreach ($classes as $class) {
            if (str_starts_with('App\\Utils\\ClassResolverData', $class)) {
                $count++;
            }
        }
        $this->assertEquals(1, $count, 'There should be one class loaded');
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
