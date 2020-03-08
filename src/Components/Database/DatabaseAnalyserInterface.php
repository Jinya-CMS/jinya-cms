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
}