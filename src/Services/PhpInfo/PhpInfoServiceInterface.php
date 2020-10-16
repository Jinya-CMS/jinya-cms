<?php

namespace Jinya\Services\PhpInfo;

use Jinya\Components\PhpInfo\PhpExtension;

interface PhpInfoServiceInterface
{
    /**
     * Gets the PHP Uname
     */
    public function getUname(): string;

    /**
     * Gets loaded extensions with their config
     *
     * @return PhpExtension[]
     */
    public function getLoadedExtensions(): array;

    /**
     * Gets the ini values not assigned to an extension
     */
    public function getIniValues(): array;

    /**
     * Gets the running php version
     */
    public function getVersion(): string;

    /**
     * Gets the version of the zend engine
     */
    public function getZendVersion(): string;

    /**
     * Gets the version of apache
     */
    public function getApacheVersion(): string;

    /**
     * Gets all apache modules
     */
    public function getApacheModules(): array;
}
