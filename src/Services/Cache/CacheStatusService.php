<?php

namespace Jinya\Services\Cache;

use Jinya\Components\Cache\CacheState;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

class CacheStatusService implements CacheStatusServiceInterface
{
    private CacheBuilderInterface $jinyaCache;

    private CacheClearerInterface $symfonyCacheClearer;

    private string $kernelProjectDir;

    /**
     * CacheStatusService constructor.
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getApcuCacheState(): ?CacheState
    {
        if (function_exists('apcu_cache_info')) {
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
     * {@inheritdoc}
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
            if ('.' !== $t && '..' !== $t) {
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
     * {@inheritdoc}
     */
    public function getJinyaCacheState(): ?CacheState
    {
        $state = new CacheState();
        $state->setFreeMemory(-1);
        $cacheFile = $this->jinyaCache->getCacheFile();
        $handle = @fopen($cacheFile, 'rb');
        if ($handle) {
            $size = 0;
            $count = 0;
            while (false !== ($line = fgets($handle))) {
                $file = str_replace("\n", '', $line);
                $size += filesize($file);
                ++$count;
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
     * {@inheritdoc}
     */
    public function clearOpCache(): void
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearApcuCache(): void
    {
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearSymfonyCache(): void
    {
        $this->symfonyCacheClearer->clear($this->getSymfonyCacheDirectory());
    }

    /**
     * {@inheritdoc}
     */
    public function clearJinyaCacheState(): void
    {
        $this->jinyaCache->clearCache();
    }
}
