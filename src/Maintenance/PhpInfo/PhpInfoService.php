<?php

namespace App\Maintenance\PhpInfo;

use Imagick;
use JetBrains\PhpStorm\Pure;
use Jinya\Database\Entity;
use PDO;
use Phar;
use ReflectionException;
use ReflectionExtension;
use stdClass;
use Transliterator;

/**
 *
 */
class PhpInfoService
{
    /**
     * Gets the unix name
     *
     * @return string
     */
    public function getUname(): string
    {
        return php_uname();
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
        array_splice($loadedExtensions, array_search('Core', $loadedExtensions) ?: 0, 1);
        natcasesort($loadedExtensions);
        array_unshift($loadedExtensions, 'Core');

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
     * Gets additional data for extensions, currently supported are zend opcache, apcu and hash
     *
     * @param string $extension
     * @return array<string, mixed>|stdClass
     */
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

        if ($lowerExt === 'date') {
            return [
                'type' => 'date',
                'defaultTimezone' => date_default_timezone_get(),
                'databaseVersion' => timezone_version_get(),
                'enabled' => true,
            ];
        }

        if ($lowerExt === 'imagick') {
            return [
                'type' => 'imagick',
                'enabled' => true,
                'version' => Imagick::getVersion()['versionString'],
                'copyright' => Imagick::getCopyright(),
                'package' => Imagick::getPackageName(),
                'releaseDate' => Imagick::getReleaseDate(),
            ];
        }

        if ($lowerExt === 'intl') {
            return [
                'type' => 'intl',
                'enabled' => true,
                'ids' => implode(', ', Transliterator::listIDs() ?: []),
            ];
        }

        if ($lowerExt === 'mbstring') {
            return [
                'type' => 'mbstring',
                'enabled' => true,
                'encodings' => implode(', ', mb_list_encodings()),
            ];
        }

        if ($lowerExt === 'pcre') {
            return [
                'type' => 'pcre',
                'enabled' => true,
                'version' => PCRE_VERSION,
            ];
        }

        if ($lowerExt === 'pdo') {
            $pdo = Entity::getPdo();
            return [
                'type' => 'pdo',
                'enabled' => true,
                'availableDrivers' => implode(', ', PDO::getAvailableDrivers()),
                'clientVersion' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
                'serverVersion' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                'serverInfo' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
                'driverName' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            ];
        }

        if ($lowerExt === 'pdo_mysql') {
            $pdo = Entity::getPdo();
            return [
                'type' => 'pdo_mysql',
                'clientVersion' => $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
            ];
        }

        if ($lowerExt === 'phar') {
            return [
                'type' => 'phar',
                'enabled' => true,
                'compressions' => implode(', ', Phar::getSupportedCompression()),
                'apiVersion' => Phar::apiVersion(),
                'signatures' => implode(', ', Phar::getSupportedSignatures()),
            ];
        }

        return new stdClass();
    }

    /**
     * Converts bytes to megabytes
     *
     * @param int $bytes
     * @return float|int
     */
    private function calculateMb(int $bytes): float|int
    {
        return $bytes / 1024 / 1024;
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
     * @codeCoverageIgnore
     */
    public function getApacheVersion(): string
    {
        if (function_exists('apache_get_version')) {
            return apache_get_version() ?: '';
        }

        return '';
    }

    /**
     * Gets all apache modules
     *
     * @return array<string>
     * @codeCoverageIgnore
     */
    public function getApacheModules(): array
    {
        if (function_exists('apache_get_modules')) {
            return apache_get_modules() ?: [];
        }

        return [];
    }
}
