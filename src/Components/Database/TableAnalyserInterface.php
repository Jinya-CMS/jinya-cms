<?php

namespace Jinya\Components\Database;

interface TableAnalyserInterface
{
    /**
     * Gets all tables
     *
     * @return array
     */
    public function getTables(): array;

    /**
     * Gets the metadata of the given table
     *
     * @param string $table
     * @return TableMetadata
     */
    public function getTableMetadata(string $table): ?TableMetadata;
}