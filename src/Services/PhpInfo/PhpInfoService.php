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

        foreach ($loadedExtensions as $extensionName) {
            try {
                $iniValues = ini_get_all($extensionName);
            } catch (\Throwable $exception) {
                $iniValues = [];
            }

            $extension = new PhpExtension();
            $extension->setExtensionName($extensionName);
            $extension->setVersion(phpversion($extensionName));

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

            $extensions[] = $extension;
        }

        return $extensions;
    }

    /**
     * @inheritDoc
     */
    public function getIniValues(): array
    {
        $iniValues = ini_get_all();

        $items = array_filter($iniValues, static function ($key) {
            return !strpos($key, '.');
        }, ARRAY_FILTER_USE_KEY);
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