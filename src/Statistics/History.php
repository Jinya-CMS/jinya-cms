<?php

namespace App\Statistics;

use App\Database\Utils\LoadableEntity;
use Iterator;

class History
{
    public function getCreatedHistory(string $type): Iterator
    {
        $history = LoadableEntity::executeSqlString(
            "SELECT COUNT(created_at) AS `count`, date(`created_at`) AS date FROM $type GROUP BY date(`created_at`)"
        );
        return LoadableEntity::hydrateMultipleResults(
            $history,
            new HistoryEntry(),
        );
    }

    public function getUpdatedHistory(string $type): Iterator
    {
        $history = LoadableEntity::executeSqlString(
            "SELECT COUNT(last_updated_at) AS count, date(`last_updated_at`) AS date FROM $type GROUP BY date(`last_updated_at`)"
        );
        return LoadableEntity::hydrateMultipleResults(
            $history,
            new HistoryEntry(),
        );
    }
}