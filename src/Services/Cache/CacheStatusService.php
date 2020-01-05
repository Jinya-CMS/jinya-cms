<?php

namespace Jinya\Services\Cache;

use Jinya\Components\Cache\CacheState;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Underscore\Types\Strings;

class CacheStatusService implements CacheStatusServiceInterface
{
    /** @var CacheBuilderInterface */
    private $jinyaCache;

    /** @var CacheClearerInterface */
    private $symfonyCacheClearer;

    /** @var string */
    private $kernelProjectDir;

    /**
     * CacheStatusService constructor.
     * @param CacheBuilderInterface $jinyaCache
     * @param CacheClearerInterface $symfonyCacheClearer
     * @param string $kernelProjectDir
     */
    public function __construct(
        CacheBuilderInterface $jinyaCache,
        CacheClearerInterface $symfonyCacheClearer,
        string $kernelProjectDir
    ) {
        $this->jinyaCache = $jinyaCache;
        $this->symfonyCacheClearer = $symfonyCacheClearer;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @inheritDoc
     */
    public function getOpCacheState(): ?CacheState
    {
        if (function_exists('opcache_get_status')) {
            $data = opcache_get_status(false);
            $statistics = $data['opcache_statistics'];
            $memoryUsage = $data['memory_usage'];
            $state = new CacheState();
            $state->setCount($statistics['num_cached_scripts'] + $statistics['num_cached_keys']);
            $state->setFreeMemory($memoryUsage['free_memory']);
            $state->setUsedMemory($memoryUsage['used_memory']);

            return $state;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getApcuCacheState(): ?CacheState
    {
        if (function_exists('opcache_get_status')) {
            $data = apcu_cache_info(true);
            $state = new CacheState();
            $state->setCount($data['num_entries']);
            $state->setFreeMemory(-1);
            $state->setUsedMemory($data['mem_size']);

            return $state;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSymfonyCacheState(): ?CacheState
    {
        $state = new CacheState();
        $state->setUsedMemory($this->getFolderSize($this->getSymfonyCacheDirectory()));
        $state->setFreeMemory(-1);
        $state->setCount(-1);

        return $state;
    }

    private function getFolderSize(string $path): int
    {
        $total_size = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/') . '/';

        foreach ($files as $t) {
            if ($t !== '.' && $t !== '..') {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $size = $this->getFolderSize($currentFile);
                    $total_size += $size;
                } else {
                    $size = filesize($currentFile);
                    $total_size += $size;
                }
            }
        }

        return $total_size;
    }

    private function getSymfonyCacheDirectory(): string
    {
        return $this->kernelProjectDir . '/var/cache';
    }

    /**
     * @inheritDoc
     */
    public function getJinyaCacheState(): ?CacheState
    {
        $state = new CacheState();
        $state->setFreeMemory(-1);
        $cacheFile = $this->jinyaCache->getCacheFile();
        $handle = fopen($cacheFile, 'rb');
        if ($handle) {
            $size = 0;
            $count = 0;
            while (($line = fgets($handle)) !== false) {
                $file = Strings::replace($line, "\n", '');
                $size += filesize($file);
                $count++;
            }
            $state->setUsedMemory($size);
            $state->setCount($count);

            fclose($handle);
        } else {
            $state->setCount(0);
            $state->setUsedMemory(0);
        }

        return $state;
    }

    /**
     * @inheritDoc
     */
    public function clearOpCache(): void
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * @inheritDoc
     */
    public function clearApcuCache(): void
    {
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
        }
    }

    /**
     * @inheritDoc
     */
    public function clearSymfonyCache(): void
    {
        $this->symfonyCacheClearer->clear($this->getSymfonyCacheDirectory());
    }

    /**
     * @inheritDoc
     */
    public function clearJinyaCacheState(): void
    {
        $this->jinyaCache->clearCache();
    }
}