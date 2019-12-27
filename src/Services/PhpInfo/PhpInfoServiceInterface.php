<?php

namespace Jinya\Services\PhpInfo;

use Jinya\Components\PhpInfo\PhpExtension;

interface PhpInfoServiceInterface
{
    /**
     * Gets the PHP Uname
     *
     * @return string
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
     *
     * @return array
     */
    public function getIniValues(): array;

    /**
     * Gets the running php version
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Gets the version of the zend engine
     *
     * @return string
     */
    public function getZendVersion(): string;

    /**
     * Gets the version of apache
     *
     * @return string
     */
    public function getApacheVersion(): string;

    /**
     * Gets all apache modules
     *
     * @return array
     */
    public function getApacheModules(): array;
}