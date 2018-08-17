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
    public const ART_GALLERY = 'art_gallery';
    public const VIDEO_GALLERY = 'video_gallery';
    public const ARTWORK = 'artwork';
    public const FORM = 'form';
    public const PAGE = 'page';

    /**
     * Builds the cache
     */
    public function buildCache(): void;

    /**
     * Builds the cache for the given routing entry
     *
     * @param RoutingEntry $routingEntry
     */
    public function buildRouteCache(RoutingEntry $routingEntry): void;

    /**
     * Builds the cache for the given slug and type
     *
     * @param string $slug
     * @param string $type
     */
    public function buildCacheBySlugAndType(string $slug, string $type): void;

    /**
     * Clears the cache
     */
    public function clearCache(): void;
}