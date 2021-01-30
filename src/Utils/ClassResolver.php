<?php

namespace App\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ClassResolver
{
    public static function loadClasses(string $searchPath): array
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchPath));
        $classesWithFullPath = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $classesWithFullPath[] = $file->getPathname();
            }
        }
        foreach ($classesWithFullPath as $path) {
            $class = 'App' . str_replace('.php', '', implode('\\', explode('/', explode('src', $path)[1])));
            class_exists($class, true);
        }

        return get_declared_classes();
    }
}