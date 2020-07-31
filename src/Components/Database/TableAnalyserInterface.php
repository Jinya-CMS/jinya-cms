<?php

namespace Jinya\Components\Database;

interface TableAnalyserInterface
{
    /**
     * Gets all tables
     */
    public function getTables(): array;

    /**
     * Gets the metadata of the given table
     *
     * @return TableMetadata
     */
    public function getTableMetadata(string $table): ?TableMetadata;
}
