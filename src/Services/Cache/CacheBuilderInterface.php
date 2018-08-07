<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 19:33
 */

namespace Jinya\Services\Cache;


interface CacheBuilderInterface
{
    /**
     * Builds the cache
     */
    public function buildCache(): void;

    /**
     * Clears the cache
     */
    public function clearCache(): void;
}