<?php

namespace Jinya\Services\PhpInfo;

use Jinya\Components\PhpInfo\IniValue;
use Jinya\Components\PhpInfo\PhpExtension;

class PhpInfoService implements PhpInfoServiceInterface
{

    /**
     * @inheritDoc
     */
    public function getUname(): string
    {
        return php_uname();
    }

    /**
     * @inheritDoc
     */
    public function getLoadedExtensions(): array
    {
        $extensions = [];
        $loadedExtensions = get_loaded_extensions();

        foreach ($loadedExtensions as $extensionName => $loadedExtension) {
            $iniValues = ini_get_all($extensionName);
            $extension = new PhpExtension();
            $extension->setExtensionName($extensionName);
            $extension->setVersion(phpversion($extensionName));

            foreach ($iniValues as $iniName => $iniValue) {
                $iniConfig = new IniValue();
                $iniConfig->setConfigName($iniName);
                $iniConfig->setValue($iniValue);
                $extension->addIniValue($iniConfig);
            }
        }

        return $extensions;
    }

    /**
     * @inheritDoc
     */
    public function getIniValues(): array
    {
        $iniValues = ini_get_all();

        return array_filter($iniValues, static function ($value) {
            return strpos($value, '.');
        });
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * @inheritDoc
     */
    public function getZendVersion(): string
    {
        return zend_version();
    }

    /**
     * @inheritDoc
     */
    public function getApacheVersion(): string
    {
        if (function_exists('apache_get_version')) {
            return apache_get_version();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getApacheModules(): array
    {
        if (function_exists('apache_get_modules')) {
            return apache_get_modules();
        }

        return [];
    }
}