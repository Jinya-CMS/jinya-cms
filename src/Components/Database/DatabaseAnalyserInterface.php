<?php

namespace Jinya\Components\Database;

interface DatabaseAnalyserInterface
{
    /**
     * Gets all global variables
     *
     * @return array
     */
    public function getGlobalVariables(): array;

    /**
     * Gets all local variables
     *
     * @return array
     */
    public function getLocalVariables(): array;

    /**
     * Gets the current database version
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Gets the database server type
     *
     * @return string
     */
    public function getServerType(): string;
}
