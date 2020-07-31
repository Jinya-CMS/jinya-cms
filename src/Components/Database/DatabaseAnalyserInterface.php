<?php

namespace Jinya\Components\Database;

interface DatabaseAnalyserInterface
{
    /**
     * Gets all global variables
     */
    public function getGlobalVariables(): array;

    /**
     * Gets all local variables
     */
    public function getLocalVariables(): array;

    /**
     * Gets the current database version
     */
    public function getVersion(): string;

    /**
     * Gets the database server type
     */
    public function getServerType(): string;
}
