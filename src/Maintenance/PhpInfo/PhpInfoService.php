<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\Pure;
use ReflectionException;
use ReflectionExtension;
use stdClass;
use Throwable;

class PhpInfoService
{
    /**
     * Gets the unix name
     *
     * @return string
     */
    #[Pure] public function getUname(): string
    {
        return php_uname();
    }

    private function calculateMb(int $bytes): int
    {
        return $bytes / 1024 / 1024;
    }

    private function getAdditionalData(string $extension): array|stdClass
    {
        $lowerExt = strtolower($extension);
        if ($lowerExt === 'zend opcache' && function_exists('opcache_get_status')) {
            $opcacheStatus = opcache_get_status();
            if (!$opcacheStatus) {
                return new stdClass();
            }

            return [
                'type' => 'opcache',
                'enabled' => $opcacheStatus['opcache_enabled'],
                'full' => $opcacheStatus['cache_full'],
                'usedMemory' => $this->calculateMb($opcacheStatus['memory_usage']['used_memory']),
                'freeMemory' => $this->calculateMb($opcacheStatus['memory_usage']['free_memory']),
                'wastedMemory' => $this->calculateMb($opcacheStatus['memory_usage']['wasted_memory']),
                'currentWastedMemoryPercentage' => $opcacheStatus['memory_usage']['current_wasted_percentage'],
                'jitEnabled' => $opcacheStatus['jit']['enabled'],
            ];
        }

        if ($lowerExt === 'apcu' && function_exists('apcu_enabled') && function_exists('apcu_cache_info')) {
            $apcuInfo = apcu_cache_info();
            if (!$apcuInfo) {
                return new stdClass();
            }

            return [
                'type' => 'apcu',
                'enabled' => apcu_enabled(),
                'numberSlots' => $apcuInfo['num_slots'],
                'numberEntries' => $apcuInfo['num_entries'],
                'memorySize' => $this->calculateMb($apcuInfo['mem_size']),
                'memoryType' => $apcuInfo['memory_type'],
            ];
        }

        if ($lowerExt === 'hash' && function_exists('hash_algos')) {
            return [
                'type' => 'hash',
                'enabled' => true,
                'algos' => implode(', ', hash_algos()),
            ];
        }

        return new stdClass();
    }

    /**
     * Gets an array of loaded extensions
     *
     * @return PhpExtension[]
     * @throws ReflectionException
     */
    public function getLoadedExtensions(): array
    {
        $extensions = [];
        $loadedExtensions = get_loaded_extensions();

        foreach ($loadedExtensions as $extensionName) {
            $extension = new PhpExtension();
            $reflectionExtension = new ReflectionExtension($extensionName);
            $iniValues = $reflectionExtension->getINIEntries();

            $extension->extensionName = $reflectionExtension->getName();
            $extension->version = $reflectionExtension->getVersion();
            $extension->additionalData = $this->getAdditionalData($extensionName);

            if ($iniValues) {
                foreach ($iniValues as $iniName => $iniValue) {
                    $iniConfig = new IniValue();
                    $iniConfig->configName = $iniName;
                    if (is_array($iniValue)) {
                        if (array_key_exists('local_value', $iniValue)) {
                            $iniConfig->value = $iniValue['local_value'];
                        } elseif (array_key_exists('global_value', $iniValue)) {
                            $iniConfig->value = $iniValue['global_value'];
                        }
                    } else {
                        $iniConfig->value = $iniValue;
                    }
                    $extension->iniValues[] = $iniConfig;
                }
            }

            $extensions[] = $extension;
        }

        return $extensions;
    }

    /**
     * Gets the PHP version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Gets the version of the Zend Engine
     *
     * @return string
     */
    #[Pure] public function getZendVersion(): string
    {
        return zend_version();
    }

    /**
     * Gets the apache version
     *
     * @return string
     */
    public function getApacheVersion(): string
    {
        if (function_exists('apache_get_version')) {
            return apache_get_version();
        }

        return '';
    }

    /**
     * Gets all apache modules
     *
     * @return array
     */
    public function getApacheModules(): array
    {
        if (function_exists('apache_get_modules')) {
            return apache_get_modules();
        }

        return [];
    }
}
