<?php

namespace Jinya\Cms\Utils;

use Jinya\Router\Router\Router;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

abstract class CacheUtils
{
    private static function invalidateCachedFiles(string $directory): void
    {
        if (function_exists('opcache_invalidate') && function_exists('opcache_is_script_cached')) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            foreach ($iterator as $item) {
                /** @var SplFileInfo $item */
                if ($item->getExtension() === 'php' && opcache_is_script_cached($item->getPathname())) {
                    opcache_invalidate($item->getPathname());
                }
            }
        }

        $fs = new Filesystem();
        $fs->remove(__JINYA_CACHE . '/jinya/database');
    }

    /**
     * Clears the jinya database cache
     *
     * @return void
     */
    public static function clearDatabaseCache(): void
    {
        self::invalidateCachedFiles(__JINYA_CACHE . '/jinya/database');
    }

    /**
     * Clears the jinya router cache
     *
     * @return void
     */
    public static function clearRouterCache(): void
    {
        self::invalidateCachedFiles(__JINYA_CACHE . '/jinya/router-extensions');
        self::invalidateCachedFiles(__JINYA_CACHE . '/routing');
    }

    /**
     * Recreates the jinya router cache
     *
     * @return void
     * @throws ReflectionException
     */
    public static function recreateRoutingCache(): void
    {
        global $__JINYA_ROUTER_CONFIGURATION;
        [$cacheDir, $controllerDir, , , $extension] = $__JINYA_ROUTER_CONFIGURATION;
        Router::buildRoutingCache(
            $cacheDir,
            $controllerDir,
            $extension
        );
    }

    /**
     * Clears the opcache, usually this is not necessary
     *
     * @return void
     */
    public static function clearOpcache(): void
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}
