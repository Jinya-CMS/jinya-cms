<?php

namespace App\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ClassResolver
{
    public static function loadClasses(string $searchPath, string $pathPrefix = 'src'): array
    {
        if (!file_exists($searchPath)) {
            return get_declared_classes();
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($searchPath));
        $classesWithFullPath = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $classesWithFullPath[] = $file->getPathname();
            }
        }
        foreach ($classesWithFullPath as $path) {
            if (str_starts_with($path, 'src')) {
                $class = 'App' . str_replace('.php', '', implode('\\', explode('/', explode($pathPrefix, $path)[1])));
                class_exists($class, true);
            }
        }

        return get_declared_classes();
    }
}
