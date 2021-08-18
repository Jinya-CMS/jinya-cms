<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\Pure;
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

    /**
     * Gets an array of loaded extensions
     *
     * @return PhpExtension[]
     */
    public function getLoadedExtensions(): array
    {
        $extensions = [];
        $loadedExtensions = get_loaded_extensions();

        foreach ($loadedExtensions as $extensionName) {
            try {
                if ($extensionName === 'Zend OPcache') {
                    $iniValues = array_filter(
                        ini_get_all(),
                        static fn($item) => str_starts_with($item, 'opcache'),
                        ARRAY_FILTER_USE_KEY,
                    );
                } else {
                    $iniValues = @ini_get_all($extensionName);
                }
            } catch (Throwable) {
                $iniValues = [];
            }

            $extension = new PhpExtension();
            $extension->setExtensionName($extensionName);
            $extension->setVersion(phpversion($extensionName));

            if ($iniValues) {
                foreach ($iniValues as $iniName => $iniValue) {
                    $iniConfig = new IniValue();
                    $iniConfig->setConfigName($iniName);
                    if (is_array($iniValue)) {
                        if (array_key_exists('local_value', $iniValue)) {
                            $iniConfig->setValue($iniValue['local_value']);
                        } elseif (array_key_exists('global_value', $iniValue)) {
                            $iniConfig->setValue($iniValue['global_value']);
                        }
                    } else {
                        $iniConfig->setValue($iniValue);
                    }
                    $extension->addIniValue($iniConfig);
                }
            }

            $extensions[] = $extension;
        }

        return $extensions;
    }

    /**
     * Gets an array of all ini values
     *
     * @return IniValue[]
     */
    public function getIniValues(): array
    {
        $iniValues = ini_get_all();

        $items = array_filter(
            $iniValues,
            static function ($key) {
                return !strpos($key, '.');
            },
            ARRAY_FILTER_USE_KEY
        );
        $values = [];

        foreach ($items as $key => $iniValue) {
            $iniConfig = new IniValue();
            if (is_array($iniValue)) {
                if (array_key_exists('local_value', $iniValue)) {
                    $iniConfig->setValue($iniValue['local_value']);
                } elseif (array_key_exists('global_value', $iniValue)) {
                    $iniConfig->setValue($iniValue['global_value']);
                }
            } else {
                $iniConfig->setValue($iniValue);
            }
            $iniConfig->setConfigName($key);
            $values[] = $iniConfig;
        }

        return $values;
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
