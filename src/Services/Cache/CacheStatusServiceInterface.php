<?php

namespace Jinya\Services\Cache;

use Jinya\Components\Cache\CacheState;

interface CacheStatusServiceInterface
{
    /**
     * Gets the current op cache state
     *
     * @return CacheState
     */
    public function getOpCacheState(): ?CacheState;

    /**
     * Gets the apcu cache state
     *
     * @return CacheState
     */
    public function getApcuCacheState(): ?CacheState;

    /**
     * Gets the symfony cache state
     *
     * @return CacheState
     */
    public function getSymfonyCacheState(): ?CacheState;

    /**
     * Gets the jinya cache state
     *
     * @return CacheState
     */
    public function getJinyaCacheState(): ?CacheState;

    /**
     * Clears the op cache
     */
    public function clearOpCache(): void;

    /**
     * Clears the apcu cache
     */
    public function clearApcuCache(): void;

    /**
     * Clears the symfony cache
     */
    public function clearSymfonyCache(): void;

    /**
     * Clears the jinya cache
     */
    public function clearJinyaCacheState(): void;
}