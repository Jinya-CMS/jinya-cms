<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\Pure;
use ReflectionExtension;
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
     * @throws \ReflectionException
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
