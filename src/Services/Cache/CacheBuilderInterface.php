<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 19:33
 */

namespace Jinya\Services\Cache;

use Jinya\Entity\Menu\RoutingEntry;

interface CacheBuilderInterface
{
    public const GALLERY = 'gallery';

    public const FORM = 'form';

    public const PAGE = 'page';

    public const SEGMENT_PAGE = 'segment_page';

    public const MEDIA_GALLERY = 'media_gallery';

    /**
     * Builds the cache
     */
    public function buildCache(): void;

    /**
     * Builds the cache for the given routing entry
     */
    public function buildRouteCache(RoutingEntry $routingEntry): void;

    /**
     * Builds the cache for the given slug and type
     */
    public function buildCacheBySlugAndType(string $slug, string $type): void;

    /**
     * Clears the cache
     */
    public function clearCache(): void;

    /**
     * Gets the cache file
     */
    public function getCacheFile(): string;
}
